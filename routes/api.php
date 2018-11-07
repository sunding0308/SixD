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

Route::group(['namespace'=>'Api'], function(){
    Route::post('/get_statistics_data', 'ApiController@getStatisticsData');

    /**
     * push signal to machine
     */
    Route::get('/push_overage_signal', 'PushController@pushOverageSignal')->middleware('check_online');
    Route::get('/push_hardware_status_signal', 'PushController@pushHardwareStatusSignal')->middleware('check_online');
    Route::get('/push_environment_signal', 'PushController@pushEnvironmentSignal')->middleware('check_online');
    Route::get('/push_water_quality_statistics_signal', 'PushController@pushWaterQualityStatisticsSignal');
    Route::get('/push_records_signal', 'PushController@pushRecordsSignal');
    // Route::post('/push_redpacket_qr_code', 'PushController@pushRedpacketQrCodeSignal');
    // Route::post('/push_redpacket_received_signal', 'PushController@pushRedpacketReceivedSignal');
    // Route::post('/push_installation_completed_signal', 'PushController@pushInstallationCompletedSignal');
    Route::get('/push_app_menu_analysis_signal', 'PushController@pushAppMenuAnalysisSignal');
    Route::get('/push_api_analysis_signal', 'PushController@pushApiAnalysisSignal');

    /**
     * push to data cloud
     */
    Route::post('/push_urgent', 'PushController@pushUrgentServiceToDataCloud');
    Route::post('/push_ordinary', 'PushController@pushOrdinaryServiceToDataCloud');
    Route::post('/push_single_ordinary_complete', 'PushController@pushSingleOrdinaryServiceCompleteToDataCloud');
    Route::post('/push_maintenance', 'PushController@pushMaintenanceToDataCloud');
    Route::post('/push_use_status', 'PushController@pushUseStatusToDataCloud');

    /**
     * apis for andriod and data cloud
     */
    Route::group(['middleware'=>'api_auth'], function(){
        // Route::get('/check_status', 'OnlineController@checkStatus');
        Route::post('/machine/register', 'OnlineController@register');
        // Route::post('/machine/installation', 'OnlineController@installation');
        // Route::post('/urgent/account_type', 'PushController@pushUrgentAccountType');
        // Route::post('/account_type', 'PushController@pushAccountType');
        // Route::post('/topup', 'TopupController@topup');
        Route::get('/vip_product', 'TopupController@getVipProduct');
        Route::post('/vip_topup', 'TopupController@vipTopup');
        // Route::post('/reset_overage', 'TopupController@resetOverage');
        Route::post('/online', 'OnlineController@online');
        Route::get('/online/user_rank', 'OnlineController@getUserRank');
        // Route::get('/online/check_reserve', 'OnlineController@checkReserve');
        Route::get('/online/machine_info', 'OnlineController@getMachineInfo');
        Route::post('/online/logfile', 'OnlineController@logfile');
        Route::post('/alarms', 'AlarmsController@alarms');
        Route::post('/realtime/overage', 'RealtimeController@overage');
        Route::post('/realtime/heartbeat', 'RealtimeController@heartbeat');
        Route::post('/report/push/received', 'ReportController@pushReceived');
        Route::post('/report/hardware_status', 'ReportController@hardwareStatus');
        Route::post('/report/records', 'ReportController@records');
        Route::post('/report/environment', 'ReportController@environment');
        Route::post('/report/water_quality_statistics', 'ReportController@waterQualityStatistics');
        Route::post('/report/app_menu_analysis', 'ReportController@appMenuAnalysis');
        Route::post('/report/api_analysis', 'ReportController@apiAnalysis');
        Route::get('/check_update', 'OnlineController@getOta');
        Route::post('/vending/stock_in', 'StockController@stockIn');
        Route::post('/vending/stock_out', 'StockController@stockOut');
    });
    Route::get('/version/download', 'OnlineController@versionDownload');
});