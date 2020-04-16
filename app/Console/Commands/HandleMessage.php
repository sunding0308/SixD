<?php

namespace App\Console\Commands;

use App\Machine;
use App\Message;
use App\Services\DubboProxyService;
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
                        $this->vendingHandleMessage($message);
                        break;
                    case Machine::TYPE_SHOEBOX_V2:
                        Log::debug(self::LOG_TAG.'shoe box['.$message->machine->device.'] '. $message->body);
                        $this->shoeboxV2HandleMessage($message);
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


    private function vendingHandleMessage($message)
    {
        $machine = $message->machine;
        $body = json_decode($message->body);
        switch ($body->message) {
            case 'stock_in':
                Log::debug(self::LOG_TAG.'stock in');
                $machine->load('stocks');
                foreach($body->products as $product) {
                    $stock = $machine->stocks->where('position', $product->pos)->first();
                    if ($stock) {
                        $stock->in($product->q);
                        $stock->save();
                    }
                }
                break;
            case 'stock_out':
                Log::debug(self::LOG_TAG.'stock out');
                $machine->load('stocks');
                foreach($body->products as $product) {
                    $stock = $machine->stocks->where('position', $product->pos)->first();
                    if ($stock) {
                        $stock->out($product->quantity);        //stock out using quantity not q
                        $stock->save();
                    }
                }
                break;
            case 'alarm':
                Log::debug(self::LOG_TAG.'alarm');
                $machine->load('alarm');
                $alarm = $machine->alarm;
                if ($alarm) {
                    $codes = explode(',', $alarm->malfunction_code);
                    $codes[] = $body->malfunction_code;
                    $codes = array_unique($codes);
                    $alarm->malfunction_code = implode(',', $codes);
                    $alarm->save();
                } else {
                    $machine->alarm()->create([
                        'malfunction_code' => $body->malfunction_code ?: '',
                    ]);
                }
                $machine->alarmHistories()->create([
                    'malfunction_code' => $body->malfunction_code ?: '',
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ]);
                break;
            case 'alarm_clear':
                Log::debug(self::LOG_TAG.'alarm_clear');
                $machine->load('alarm');
                $alarm = $machine->alarm;
                if ($alarm) {
                    $codes = explode(',', $alarm->malfunction_code);
                    if (($key = array_search($body->malfunction_code, $codes)) !== false) {
                        unset($codes[$key]);
                        $alarm->malfunction_code = implode(',', $codes);
                        $alarm->malfunction_code = trim($alarm->malfunction_code, ',');
                        $alarm->save();
                    }
                }
                if ($body->malfunction_code == 'e2') {
                    /* Special for e2: 2G error, this error won't generate alarm when happened,
                     * so we need create it here
                     */
                    $machine->alarmHistories()->create([
                        'malfunction_code' => $body->malfunction_code ?: '',
                        'created_at' => $message->created_at->subSecond(),
                        'updated_at' => $message->updated_at->subSecond(),
                    ]);
                }
                $machine->alarmHistories()->create([
                    'malfunction_code' => $body->malfunction_code ?: '',
                    'cleared' => 1,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ]);
                break;
            default:
                break;
        }
    }

    private function shoeboxV2HandleMessage($message)
    {
        $service = DubboProxyService::getInfoService();
        $machine = $message->machine;
        $body = json_decode($message->body);
        switch ($body->opt) {
            case 2:
                Log::debug(self::LOG_TAG.'startStop'.$body->value);
                //start stop
                $service->machineStart(
                    (string)$machine->machine_id,
                    (string)$body->value,
                    (string)$body->m,
                    (string)$body->t,
                    (string)$body->appt);
                break;
            case 3:
                Log::debug(self::LOG_TAG.'childlock '.$body->value);
                //childlock
                $service->machineLock((string)$machine->machine_id, (string)$body->value);
                break;
            case 4:
                //sleep
                Log::debug(self::LOG_TAG.'sleep '.$body->value);
                //childlock
                $service->machineDormancy((string)$machine->machine_id, (string)$body->value);
                break;
            default:
                break;
        }
    }
}
