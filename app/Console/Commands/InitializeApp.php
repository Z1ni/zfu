<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializeApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'First time setup';

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
     */
    public function handle()
    {
        $this->call('key:generate');
        $this->info('Zini\'s File Upload | First time setup');
        $this->line('Welcome! We\'ll now initialize the database and create the admin user.');
        $this->info('Initializing database...');
        $this->call('migrate');
        $this->info('Database initialized!');
        $this->call('upload:newuser');
        $this->call('clear-compiled');
        $this->call('optimize');
        $this->info('Setup complete! File upload is now ready to use!');
    }
}
