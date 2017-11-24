<?php

namespace App\Http\ViewComposers;

use App\Models\User;
use Sentinel;
use Illuminate\View\View;

class ViSudoViewComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('allUsers', $this->getUsers());
        $view->with('hasSudoed', $this->hasSudoed());
        $view->with('originalUser', $this->getOriginalUser());
        $view->with('currentUser', $this->getCurrentUser());
    }

    protected function getCurrentUser()
    {
        return Sentinel::getUser();
    }

    protected function getOriginalUser()
    {
        if (! $this->hasSudoed()) {
            return Sentinel::getUser();
        }

        $userId = session('wh.sudosu.original_id');

        return Sentinel::findById($userId);
    }

    protected function hasSudoed()
    {
        return session()->has('wh.sudosu.has_sudoed');
    }

    protected function getUsers()
    {
        return User::orderBy('email')->pluck('email', 'id')->all();
    }
}