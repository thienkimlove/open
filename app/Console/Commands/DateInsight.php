<?php

namespace App\Console\Commands;

use App\Lib\Helpers;
use App\Models\Element;
use Illuminate\Console\Command;

class DateInsight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'date:insight';

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
        #start get insight for today.

        $activeElements = Element::where('social_type', config('system.social_type.facebook'))
            ->whereHas('content', function($q) {
                $q->where('status', true);
            })
            ->get();


        # for elements for one account we choose 50
        if ($activeElements->count() > 0) {
            //dd($activeElements->count());
            foreach ($activeElements->groupBy('content_id') as $groupElements) {
               foreach ($groupElements->chunk(50) as $elements) {
                   Helpers::batchInsight($elements);
               }
            }
        }

    }
}
