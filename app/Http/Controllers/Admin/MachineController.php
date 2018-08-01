<?php

namespace App\Http\Controllers\Admin;

use App\Machine;
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
        Machine::where('id',$machine->id)->update([
            'hot_water_overage' => 0,
            'cold_water_overage' => 0,
            'oxygen_overage' => 0,
            'air_overage' => 0,
            'humidity_overage' => 0,
        ]);

        //push reset data to machine
        $response = $jpush->push($machine->registration_id, 'topup', $machine->device, [0,0,0,0,0]);
        if ($response['http_code'] == 200) {
            Log::info('Device '.$machine->device.' reset success!');
            session()->flash('success', '清除余量成功.');
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
