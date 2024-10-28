<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate single auth key for sanctum';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = User::first()->createToken('auth_token')->plainTextToken;
        $this->info('Key generated successfully');
        $this->warn('Please note that this key will be shown only once, so make sure you copy it');
        $this->warn('API key: '.$token);
    }
}
