<?php

namespace App\Console\Commands;

use App\Models\Insight;
use Illuminate\Console\Command;

class CalculateResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insight:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Result for insight';

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
        $insights = Insight::get();

        foreach ($insights as $insight) {
            if (in_array($insight->json_data['objective'], array_keys(config('system.insight.map')))) {
                if (!isset($insight->json_data['unique_actions'])) {
                    // status = NOT_ENOUGH_IMPRESSIONS
                    continue;
                }

                $actions = $insight->json_data['unique_actions'];

                foreach ($actions as $action) {
                    if ($action['action_type'] == config('system.insight.map.'.$insight->json_data['objective'])) {
                        $insight->update([
                            'result' => $action['value']
                        ]);

                        break;
                    }
                }
            } else {
               // dd($insight->json_data);
            }
        }
    }
}
