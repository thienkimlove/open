<?php

namespace App\Lib;

use App\Models\Content;
use App\Models\Department;
use App\Models\User;
use Facades\App\Models\Role;
use Carbon\Carbon;
use Sentinel;
use Facebook\Facebook;

class Helpers {

    public static function getAdAccountFields() {
        return array(
            'account_id',
            'name',
            'amount_spent',
            'balance',
            'currency',
            'min_campaign_group_spend_cap',
            'min_daily_budget',
            'next_bill_date',
            'spend_cap',
        );
    }

    public static function getRedirectFb()
    {
        $fb = new Facebook([
            'app_id' => config('system.facebook.app_id'),
            'app_secret' => config('system.facebook.app_secret'),
            'default_graph_version' => 'v2.11',
            'http_client_handler' => 'stream'
        ]);

        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl(url('/'),  ['ads_management']);
    }

    public static function roleList()
    {
        $currentUser = Sentinel::getUser();

        if ($currentUser->isManager()) {
            return Role::where('slug', 'nhan-vien')->pluck('name', 'id')->all();
        }

       return Role::pluck('name', 'id')->all();
    }

    public static function userList()
    {
        return User::pluck('name', 'id')->all();
    }

    public static function departmentList()
    {
        $currentUser = Sentinel::getUser();

        if ($currentUser->isManager()) {
            return Department::where('status', 1)->where('id', $currentUser->department_id)->pluck('name', 'id')->toArray();
        }

        return Department::where('status', 1)->pluck('name', 'id')->toArray();
    }


    public static function contentListForCreate()
    {
        return Content::whereNull('user_id')->pluck('social_name', 'id')->all();
    }

    public static function contentListForUpdate()
    {
        $contents = Content::all();

        $data = [];

        foreach ($contents as $content) {
            $data[$content->id] = $content->social_name;

            if ($content->user_id) {
                $data[$content->id] .= ' Owned by user '.$content->user->name;
            }
        }
        return $data;
    }

    public static function getListUserInGroup()
    {
        $user = Sentinel::getUser();

        if ($user->isAdmin()) {
            return User::where('status', 1)
                ->pluck('name', 'id')
                ->all();
        }

        if ($user->isManager()) {
            return User::where('status', 1)
                ->where('department_id', $user->department_id)
                ->pluck('name', 'id')
                ->all();
        }

        return [$user->id => $user->name];
    }

    public static function inDeepArray($key, $value, $ars)
    {
        $in = false;
        foreach ($ars as $item) {
            if (isset($item[$key]) && $item[$key] == $value) {
                $in = true;
            }
        }
        return $in;
    }

    public static function formatDatetime($datetime)
    {
        return $datetime ? Carbon::parse($datetime)->format('H:i:s d/m/Y') : 'Không có thông tin';
    }

    public static function toNum($value) {
        if (!$value) {
            return 0;
        } else {
            return intval(trim($value));
        }
    }

    public static function appendToLog($message, $log_file)
    {
        @file_put_contents($log_file, $message."\n", FILE_APPEND);
    }

    public static function convertDateToVietnamese( $format, $time = 0 )
    {
        if ( ! $time ) $time = time();

        $lang = array();
        $lang['sun'] = 'CN';
        $lang['mon'] = 'T2';
        $lang['tue'] = 'T3';
        $lang['wed'] = 'T4';
        $lang['thu'] = 'T5';
        $lang['fri'] = 'T6';
        $lang['sat'] = 'T7';
        $lang['sunday'] = 'Chủ nhật';
        $lang['monday'] = 'Thứ hai';
        $lang['tuesday'] = 'Thứ ba';
        $lang['wednesday'] = 'Thứ tư';
        $lang['thursday'] = 'Thứ năm';
        $lang['friday'] = 'Thứ sáu';
        $lang['saturday'] = 'Thứ bảy';
        $lang['january'] = 'Tháng Một';
        $lang['february'] = 'Tháng Hai';
        $lang['march'] = 'Tháng Ba';
        $lang['april'] = 'Tháng Tư';
        $lang['may'] = 'Tháng Năm';
        $lang['june'] = 'Tháng Sáu';
        $lang['july'] = 'Tháng Bảy';
        $lang['august'] = 'Tháng Tám';
        $lang['september'] = 'Tháng Chín';
        $lang['october'] = 'Tháng Mười';
        $lang['november'] = 'Tháng M. Một';
        $lang['december'] = 'Tháng M. Hai';
        $lang['jan'] = 'T01';
        $lang['feb'] = 'T02';
        $lang['mar'] = 'T03';
        $lang['apr'] = 'T04';
        $lang['may2'] = 'T05';
        $lang['jun'] = 'T06';
        $lang['jul'] = 'T07';
        $lang['aug'] = 'T08';
        $lang['sep'] = 'T09';
        $lang['oct'] = 'T10';
        $lang['nov'] = 'T11';
        $lang['dec'] = 'T12';

        $format = str_replace( "r", "D, d M Y H:i:s O", $format );
        $format = str_replace( array( "D", "M" ), array( "[D]", "[M]" ), $format );
        $return = date( $format, $time );

        $replaces = array(
            '/\[Sun\](\W|$)/' => $lang['sun'] . "$1",
            '/\[Mon\](\W|$)/' => $lang['mon'] . "$1",
            '/\[Tue\](\W|$)/' => $lang['tue'] . "$1",
            '/\[Wed\](\W|$)/' => $lang['wed'] . "$1",
            '/\[Thu\](\W|$)/' => $lang['thu'] . "$1",
            '/\[Fri\](\W|$)/' => $lang['fri'] . "$1",
            '/\[Sat\](\W|$)/' => $lang['sat'] . "$1",
            '/\[Jan\](\W|$)/' => $lang['jan'] . "$1",
            '/\[Feb\](\W|$)/' => $lang['feb'] . "$1",
            '/\[Mar\](\W|$)/' => $lang['mar'] . "$1",
            '/\[Apr\](\W|$)/' => $lang['apr'] . "$1",
            '/\[May\](\W|$)/' => $lang['may2'] . "$1",
            '/\[Jun\](\W|$)/' => $lang['jun'] . "$1",
            '/\[Jul\](\W|$)/' => $lang['jul'] . "$1",
            '/\[Aug\](\W|$)/' => $lang['aug'] . "$1",
            '/\[Sep\](\W|$)/' => $lang['sep'] . "$1",
            '/\[Oct\](\W|$)/' => $lang['oct'] . "$1",
            '/\[Nov\](\W|$)/' => $lang['nov'] . "$1",
            '/\[Dec\](\W|$)/' => $lang['dec'] . "$1",
            '/Sunday(\W|$)/' => $lang['sunday'] . "$1",
            '/Monday(\W|$)/' => $lang['monday'] . "$1",
            '/Tuesday(\W|$)/' => $lang['tuesday'] . "$1",
            '/Wednesday(\W|$)/' => $lang['wednesday'] . "$1",
            '/Thursday(\W|$)/' => $lang['thursday'] . "$1",
            '/Friday(\W|$)/' => $lang['friday'] . "$1",
            '/Saturday(\W|$)/' => $lang['saturday'] . "$1",
            '/January(\W|$)/' => $lang['january'] . "$1",
            '/February(\W|$)/' => $lang['february'] . "$1",
            '/March(\W|$)/' => $lang['march'] . "$1",
            '/April(\W|$)/' => $lang['april'] . "$1",
            '/May(\W|$)/' => $lang['may'] . "$1",
            '/June(\W|$)/' => $lang['june'] . "$1",
            '/July(\W|$)/' => $lang['july'] . "$1",
            '/August(\W|$)/' => $lang['august'] . "$1",
            '/September(\W|$)/' => $lang['september'] . "$1",
            '/October(\W|$)/' => $lang['october'] . "$1",
            '/November(\W|$)/' => $lang['november'] . "$1",
            '/December(\W|$)/' => $lang['december'] . "$1" );

        return preg_replace( array_keys( $replaces ), array_values( $replaces ), $return );
    }

    public static function convertDateToDisplayOrderDetail($time)
    {
        return self::convertDateToVietnamese('l', $time).' '.self::convertDateToVietnamese('d/m/Y', $time);
    }

    public static function br2nl($input)
    {
        return preg_replace('/<br\s?\/?>/ius', "\n", str_replace("\n", "", str_replace("\r", "", htmlspecialchars_decode($input))));
    }
}
