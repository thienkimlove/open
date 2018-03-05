<?php

namespace App\Console\Commands;

use App\Lib\Helpers;
use App\Models\Account;
use App\Models\Content;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:limit';

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
      # Helpers::testAccount(3);
        #find the user_id for accounts.

        $accounts = Account::all();
        foreach ($accounts as $account) {
            if ($account->user_id == null) {
                $content = Content::where('account_id', $account->id)->whereNotNull('user_id')->first();
                if ($content) {
                    $account->update(['user_id' => $content->user_id]);
                }
            }
        }
    }
}
