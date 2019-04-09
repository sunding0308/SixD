<?php

namespace App\Http\Controllers\Admin;

use App\Machine;
use Carbon\Carbon;
use App\PushRecord;
use App\Services\IotService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $hotel = $request->input('hotel');
        $room = $request->input('room');
        $alarm = $request->input('alarm');

        $machines = Machine::where('type', $request->type)
            ->whereHas('installation')
            ->with('installation')
            ->with('stocks')
            ->withCount(['alarm' => function ($query) {
                $query->where('position_change_alarm', '<>', '')
                ->orWhere('service_alarm_status', '<>', '')
                ->orWhere('sterilization_alarm', '<>', '')
                ->orWhere('filter_alarm', '<>', '')
                ->orWhere('water_shortage_alarm', '<>', '')
                ->orWhere('filter_anti_counterfeiting_alarm', '<>', '')
                ->orWhere('slave_mobile_alarm', '<>', '')
                ->orWhere('dehumidification_tank_full_water_alarm', '<>', '')
                ->orWhere('malfunction_code', '<>', '');
            }])
            ->when($hotel, function ($query) use ($hotel) {
                return $query->whereHas('installation', function($q) use ($hotel) {
                    $q->where('hotel_name', $hotel);
                });
            })
            ->when($room, function ($query) use ($room) {
                return $query->whereHas('installation', function($q) use ($room) {
                    $q->where('room', 'like', '%'.$room.'%');
                });
            })
            ->when($alarm, function ($query) {
                return $query->whereHas('alarm', function($q) {
                    $q->where('position_change_alarm', '<>', '')
                    ->orWhere('service_alarm_status', '<>', '')
                    ->orWhere('sterilization_alarm', '<>', '')
                    ->orWhere('filter_alarm', '<>', '')
                    ->orWhere('water_shortage_alarm', '<>', '')
                    ->orWhere('filter_anti_counterfeiting_alarm', '<>', '')
                    ->orWhere('slave_mobile_alarm', '<>', '')
                    ->orWhere('dehumidification_tank_full_water_alarm', '<>', '')
                    ->orWhere('malfunction_code', '<>', '');
                });
            })
            ->paginate(10);
        if (Machine::TYPE_WATER == $request->type) {
            return view('admin.pages.machine.water.index', compact('machines'));
        } else if (Machine::TYPE_VENDING == $request->type) {
            $hotelNames = array_filter(array_unique($machines->pluck('installation.hotel_name')->toArray()));
            return view('admin.pages.machine.vending.index', compact('machines', 'hotelNames', 'hotel', 'room', 'alarm'));
        } else if (Machine::TYPE_OXYGEN == $request->type) {
            return view('admin.pages.machine.oxygen.index', compact('machines'));
        } else if (Machine::TYPE_WASHING == $request->type) {
            return view('admin.pages.machine.washing.index', compact('machines'));
        } else if (Machine::TYPE_RELENISHMENT == $request->type) {
            return view('admin.pages.machine.relenishment.index', compact('machines'));
        } else if (Machine::TYPE_SHOEBOX == $request->type) {
            return view('admin.pages.machine.shoebox.index', compact('machines'));
        } else if (Machine::TYPE_TOILET_LID == $request->type) {
            return view('admin.pages.machine.toilet_lid.index', compact('machines'));
        }
    }

    public function show(Request $request, Machine $machine)
    {
        if (Machine::TYPE_VENDING == $request->type) {
            return view('admin.pages.machine.vending.show', compact('machine'));
        }
        if (809 == $request->sign) {
            return view('admin.pages.machine.relenishment.show');
        }
        $machine->load('bluetoothRecords', 'sterilization', 'waterQualityStatistics', 'waterRecords', 'airRecords', 'oxygenRecords', 'humidityRecords');
        return view('admin.pages.machine.water.show', compact('machine'));
    }

    public function waterQualityStatistics(Request $request, Machine $machine)
    {
        return view('admin.pages.machine.water.water_quality_statistics', compact('machine'));
    }

    public function bluetoothRecords(Request $request, Machine $machine)
    {
        $bluetoothRecords = $machine->bluetoothRecords()->paginate(10);
        return view('admin.pages.machine.water.bluetooth_records', compact('machine', 'bluetoothRecords'));
    }

    public function waterRecords(Request $request, Machine $machine)
    {
        $waterRecords = $machine->waterRecords()->paginate(10);
        return view('admin.pages.machine.water.water_records', compact('machine', 'waterRecords'));
    }

    public function airRecords(Request $request, Machine $machine)
    {
        $airRecords = $machine->airRecords()->paginate(10);
        return view('admin.pages.machine.water.air_records', compact('machine', 'airRecords'));
    }

    public function oxygenRecords(Request $request, Machine $machine)
    {
        $oxygenRecords = $machine->oxygenRecords()->paginate(10);
        return view('admin.pages.machine.water.oxygen_records', compact('machine', 'oxygenRecords'));
    }

    public function humidityRecords(Request $request, Machine $machine)
    {
        $humidityRecords = $machine->humidityRecords()->paginate(10);
        return view('admin.pages.machine.water.humidity_records', compact('machine', 'humidityRecords'));
    }

    public function debug(Request $request, Machine $machine)
    {
        $filenames = Storage::files('public/' . $machine->device);
        foreach($filenames as $filename) {
            $fileCreationDate = Storage::lastModified($filename);
            $arr[pathinfo($filename, PATHINFO_FILENAME)] = $fileCreationDate;
        }
        arsort($arr);
        $files = $this->paginate(collect($arr), 10);
        $files->withPath(env('APP_URL').'/admin/machine/'.$machine->id.'/debug');
        return view('admin.pages.machine.debug', compact('machine', 'files'));
    }

    public function debugDownload(Request $request, Machine $machine)
    {
        return Storage::download('public/' . $machine->device . '/' . $request->filename);
    }

    public function cleanOverage(Request $request, Machine $machine, IotService $iot)
    {
        if ($machine->type == Machine::TYPE_OXYGEN) {
            $response = $iot->rrpcForClear($machine->device);
            if ($response->Success) {
                Machine::where('id',$machine->id)->update([
                    'oxygen_overage' => 0
                ]);
                Log::info('Device '.$machine->device.' reset success!');
                
                session()->flash('success', '清除余量成功.');
            } else {
                session()->flash('error', '清除余量失败，请稍后再试！');
            }
        } else if ($machine->type == Machine::TYPE_WASHING) {
            $response = $iot->rrpcForClear($machine->device);
            if ($response->Success) {
                $machine->washingTime->update([
                    'used_time' => 0,
                    'remain_time' => 0,
                ]);
                Log::info('Device '.$machine->device.' reset success!');
                
                session()->flash('success', '清除余量成功.');
            } else {
                session()->flash('error', '清除余量失败，请稍后再试！');
            }
        } else if ($machine->type == Machine::TYPE_SHOEBOX) {
            $response = $iot->rrpcForClear($machine->device);
            if ($response->Success) {
                $machine->shoeboxTime->update([
                    'used_time' => 0,
                    'remain_time' => 0,
                ]);
                Log::info('Device '.$machine->device.' reset success!');
                
                session()->flash('success', '清除余量成功.');
            } else {
                session()->flash('error', '清除余量失败，请稍后再试！');
            }
        } else if ($machine->type == Machine::TYPE_TOILET_LID) {
            $response = $iot->rrpcForClear($machine->device);
            if ($response->Success) {
                $machine->toiletLidTime->update([
                    'used_time' => 0,
                    'remain_time' => 0,
                ]);
                Log::info('Device '.$machine->device.' reset success!');
                
                session()->flash('success', '清除余量成功.');
            } else {
                session()->flash('error', '清除余量失败，请稍后再试！');
            }
        } else {
            //push reset data to machine
            $response = $iot->rrpcToWater(Machine::SIGNAL_RESET, $machine->device, [0,0,0,0,0,0,0,0]);
            if ($response['Success']) {
                if ('success' == $response['status']) {
                    Machine::where('id',$machine->id)->update([
                        'hot_water_overage' => $response['data']['overage'][0],
                        'cold_water_overage' => $response['data']['overage'][1],
                        'oxygen_overage' => $response['data']['overage'][2],
                        'air_overage' => $response['data']['overage'][3],
                        'humidity_add_overage' => $response['data']['overage'][4],
                        'humidity_minus_overage' => $response['data']['overage'][5],
                        'humidity_child_overage' => $response['data']['overage'][6],
                        'humidity_adult_overage' => $response['data']['overage'][7],
                    ]);
                    Log::info('Device '.$machine->device.' reset success!');
                    
                    session()->flash('success', '清除余量成功.');
                } else {
                    Log::error(reset.'--Error: '.$response['message']);

                    session()->flash('error', '清除余量失败，请稍后再试！');
                }
            } else {
                session()->flash('error', '清除余量失败，请稍后再试！');
            }
        }

        return back();
    }

    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
