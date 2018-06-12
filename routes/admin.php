<?php

Route::get('/', function () {
    return view('admin.home');
})->name('home');
Route::resource('/machine', 'Admin\MachineController')->only('index', 'show');
Route::get('/machine/{machine}/water_quality_statistics', 'Admin\MachineController@waterQualityStatistics')->name('machine.water_quality_statistics');
Route::get('/machine/{machine}/bluetooth_records', 'Admin\MachineController@bluetoothRecords')->name('machine.bluetooth.records');
Route::get('/machine/{machine}/water_records', 'Admin\MachineController@waterRecords')->name('machine.water.records');
Route::get('/machine/{machine}/air_records', 'Admin\MachineController@airRecords')->name('machine.air.records');
Route::get('/machine/{machine}/oxygen_records', 'Admin\MachineController@oxygenRecords')->name('machine.oxygen.records');
Route::get('/machine/{machine}/humidity_records', 'Admin\MachineController@humidityRecords')->name('machine.humidity.records');
