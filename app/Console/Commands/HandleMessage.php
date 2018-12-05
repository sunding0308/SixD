<?php

namespace App\Console\Commands;

use App\Message;
use Illuminate\Console\Command;

class HandleMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'handle unread messages from messages table';

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
        $messages = Message::all();
        foreach($messages as $message) {
            dd(json_decode($message->body)->data->status);
        }
    }
}
