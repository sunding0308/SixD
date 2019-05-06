<?php

namespace App\Console\Commands;

use App\Machine;
use App\Message;
use App\VendingMachineStock;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandleMessage extends Command
{
    const LOG_TAG = '[message_handler]: ';

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
        $messages = Message::with('machine')->limit(50)->get();
        foreach($messages as $message) {
            try {
                switch ($message->machine->type) {
                    case Machine::TYPE_VENDING:
                        Log::debug(self::LOG_TAG.'vending machine['.$message->machine->device.'] '. $message->body);
                        $this->vendingHandleMessage($message->machine, $message->body);
                        break;
                    default:
                        break;
                }
            } catch (Exception $e) {
                Log::error(self::LOG_TAG.$e->getMessage());
            }
            $message->delete();
        }
    }


    private function vendingHandleMessage($machine, $message)
    {
        $message = json_decode($message);
        switch ($message->message) {
            case 'stock_in':
                Log::debug(self::LOG_TAG.'stock in');
                $machine->load('stocks');
                foreach($message->products as $product) {
                    $stock = $machine->stocks->where('position', $product->pos)->first();
                    if ($stock) {
                        $stock->in($product->q);
                        $stock->save();
                    }
                }
                break;
            case 'stock_out':
                Log::debug(self::LOG_TAG.'stock in');
                $machine->load('stocks');
                foreach($message->products as $product) {
                    $stock = $machine->stocks->where('position', $product->pos)->first();
                    if ($stock) {
                        $stock->out($product->quantity);        //stock out using quantity not q
                        $stock->save();
                    }
                }
                break;
            default:
                break;
        }
    }

}
