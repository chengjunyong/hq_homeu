<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunSyncJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:syncStock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Stock';

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
        exit('done');
    }
}
