<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class CreateNewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:newuser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new upload user';

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
        $this->info('New user creation');

        $username = $this->ask('New username');
        $email = $this->ask('Email');
        $passwd = bcrypt($this->secret('Password'));
        $this->info('Creating a new user...');
        $apiKey = str_random(60);
        User::create([
            'name' => $username,
            'email' => $email,
            'password' => $passwd,
            'api_token' => $apiKey
        ]);
        $this->info('New user "'.$username.'" created!');
        $this->info('API key for this user: "'.$apiKey.'"');
    }
}
