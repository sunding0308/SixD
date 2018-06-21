<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/get_statistics_data', 'Api\ApiController@getStatisticsData');

//push signal
Route::get('/push_topup_signal', 'Api\PushController@pushTopupSignal');
Route::get('/push_alarms_signal', 'Api\PushController@pushAlarmsSignal');
Route::get('/push_overage_signal', 'Api\PushController@pushOverageSignal');
Route::get('/push_hardware_status_signal', 'Api\PushController@pushHardwareStatusSignal');
Route::get('/push_records_signal', 'Api\PushController@pushRecordsSignal');
Route::get('/push_environment_signal', 'Api\PushController@pushEnvironmentSignal');
Route::get('/push_water_quality_statistics_signal', 'Api\PushController@pushWaterQualityStatisticsSignal');
Route::get('/push_app_menu_analysis_signal', 'Api\PushController@pushAppMenuAnalysisSignal');
Route::get('/push_api_analysis_signal', 'Api\PushController@pushApiAnalysisSignal');

Route::get('/topup', 'Api\TopupController@topup');
Route::post('/online', 'Api\OnlineController@online');
Route::post('/alarms', 'Api\AlarmsController@alarms');
Route::post('/realtime/overage', 'Api\RealtimeController@overage');
Route::post('/report/hardware_status', 'Api\ReportController@hardwareStatus');
Route::post('/report/records', 'Api\ReportController@records');
Route::post('/report/environment', 'Api\ReportController@environment');
Route::post('/report/water_quality_statistics', 'Api\ReportController@waterQualityStatistics');
Route::post('/report/app_menu_analysis', 'Api\ReportController@appMenuAnalysis');
Route::post('/report/api_analysis', 'Api\ReportController@apiAnalysis');