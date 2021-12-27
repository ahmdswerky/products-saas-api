<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ApplicationSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run nessassary commands for application';

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
     * @return int
     */
    public function handle()
    {
        if (App::isProduction()) {
            return $this->error('This command can\'t be run in production');
        }

        $this->info('Running Migrations ...');
        $this->call('migrate:fresh');
        $this->info('[🎉] Migrations');

        $this->info('Fetching Currencies ...');
        $this->call('currency:fetch');
        $this->info('[🎉] Currencies');

        $this->info('Running Seeders ...');
        $this->call('db:seed');
        $this->info('[🎉] Seeders');

        $this->info('Running Oauth ...');
        $this->call('passport:install');
        $this->info('[🎉] Oauth');
    }
}
