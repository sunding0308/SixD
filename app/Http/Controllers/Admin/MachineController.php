<?php

namespace App\Http\Controllers\Admin;

use App\Machine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $machine->load('bluetoothRecords', 'sterilization', 'waterRecords', 'airRecords', 'oxygenRecords', 'humidityRecords');
        return view('admin.pages.machine.show', compact('machine'));
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
}
