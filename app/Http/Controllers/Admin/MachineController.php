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
        $machines = Machine::where('type', $request->type)->paginate(10);
        if (Machine::TYPE_WATER == $request->type) {
            return view('admin.pages.machine.water.index', compact('machines'));
        } elseif (Machine::TYPE_VENDING == $request->type) {
            return view('admin.pages.machine.vending.index', compact('machines'));
        } else {
            return view('admin.pages.machine.oxygen.index', compact('machines'));
        }
    }

    public function show(Request $request, Machine $machine)
    {
        if (808 == $request->sign) {
            return view('admin.pages.machine.vending.show');
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
        $files = $this->paginate(
            collect(Storage::files('public/' . $machine->device))
                ->sortByDesc(function ($file) {
                    return pathinfo($file, PATHINFO_FILENAME);
                })
            , 10);
        return view('admin.pages.machine.debug', compact('machine', 'files'));
    }

    public function debugDownload(Request $request, Machine $machine)
    {
        return Storage::download('public/' . $machine->device . '/' . $request->filename);
    }

    public function cleanOverage(Request $request, Machine $machine, IotService $iot)
    {
        //push reset data to machine
        $response = $iot->rrpc(Machine::SIGNAL_RESET, $machine->device, [0,0,0,0,0,0,0,0]);
        if ($response['Success']) {
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
            return back();
        } else {
            session()->flash('error', '清除余量失败，请稍后再试！');
            return back();
        }
    }

    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
