<?php

namespace App\Http\Controllers\Admin;

use App\Machine;
use Carbon\Carbon;
use App\PushRecord;
use Illuminate\Http\Request;
use App\Services\JPushService;
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
    public function index()
    {
        $machines = Machine::paginate(10);
        return view('admin.pages.machine.index', compact('machines'));
    }

    public function show(Machine $machine)
    {
        $machine->load('bluetoothRecords', 'sterilization', 'waterQualityStatistics', 'waterRecords', 'airRecords', 'oxygenRecords', 'humidityRecords');
        return view('admin.pages.machine.show', compact('machine'));
    }

    public function waterQualityStatistics(Request $request, Machine $machine)
    {
        return view('admin.pages.machine.water_quality_statistics', compact('machine'));
    }

    public function bluetoothRecords(Request $request, Machine $machine)
    {
        $bluetoothRecords = $machine->bluetoothRecords()->paginate(10);
        return view('admin.pages.machine.bluetooth_records', compact('machine', 'bluetoothRecords'));
    }

    public function waterRecords(Request $request, Machine $machine)
    {
        $waterRecords = $machine->waterRecords()->paginate(10);
        return view('admin.pages.machine.water_records', compact('machine', 'waterRecords'));
    }

    public function airRecords(Request $request, Machine $machine)
    {
        $airRecords = $machine->airRecords()->paginate(10);
        return view('admin.pages.machine.air_records', compact('machine', 'airRecords'));
    }

    public function oxygenRecords(Request $request, Machine $machine)
    {
        $oxygenRecords = $machine->oxygenRecords()->paginate(10);
        return view('admin.pages.machine.oxygen_records', compact('machine', 'oxygenRecords'));
    }

    public function humidityRecords(Request $request, Machine $machine)
    {
        $humidityRecords = $machine->humidityRecords()->paginate(10);
        return view('admin.pages.machine.humidity_records', compact('machine', 'humidityRecords'));
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

    public function cleanOverage(Request $request, Machine $machine, JPushService $jpush)
    {
        if (floor((strtotime(Carbon::now())-strtotime($machine->updated_at))%86400/60) > 30) {
            session()->flash('error', '设备未在线，请开启后尝试！');
            return back();
        }
        //push reset data to machine
        $pushed_at = Carbon::now()->timestamp;
        $response = $jpush->push($machine->registration_id, Machine::SIGNAL_RESET, $pushed_at, $machine->device, [0,0,0,0,0,0,0,0]);
        if ($response['http_code'] == 200) {
            PushRecord::create([
                'machine_id' => $machine->id,
                'type' => 'reset',
                'pushed_at' => $pushed_at,
            ]);
            session()->flash('success', '清除余量成功.');
            sleep(3);
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
