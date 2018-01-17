<?php

namespace App\Console\Commands;

use App\Lib\Helpers;
use App\Models\Content;
use Illuminate\Console\Command;


class GetElement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:element';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        #For facebook only

        $adAccounts = Content::whereNotNull('user_id')
            ->where('social_type', config('system.social_type.facebook'))
            ->get();

        foreach ($adAccounts as $adAccount) {
            Helpers::fetchAccountElements($adAccount);
        }
    }
}
