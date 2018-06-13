<?php

Route::get('/', function () {
    return redirect()->route('admin.machine.index');
})->name('home');
Route::resource('/machine', 'Admin\MachineController')->only('index', 'show');
Route::group(['prefix' => '/machine', 'as' => 'machine.', 'namespace' => 'Admin'], function () {
    Route::get('/{machine}/water_quality_statistics', 'MachineController@waterQualityStatistics')->name('water_quality_statistics');
    Route::get('/{machine}/bluetooth_records', 'MachineController@bluetoothRecords')->name('bluetooth.records');
    Route::get('/{machine}/water_records', 'MachineController@waterRecords')->name('water.records');
    Route::get('/{machine}/air_records', 'MachineController@airRecords')->name('air.records');
    Route::get('/{machine}/oxygen_records', 'MachineController@oxygenRecords')->name('oxygen.records');
    Route::get('/{machine}/humidity_records', 'MachineController@humidityRecords')->name('humidity.records');
});
Route::resource('/user', 'Admin\UserController')->except('show');
