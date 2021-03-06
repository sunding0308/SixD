@extends('admin.layout')

@push('title')
共享一体机详情
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
        <div class="form">
            <div class="title-header">
                <a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_WATER]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.back') }}</a>
                <div class="title">{{ __('admin/machine.detail') }}</div>
            </div>
            @if($machine->hasAlarms())
            <div class="alert alert-detail alert-danger" role="alert">
                @if($machine->alarm->position_change_alarm)
                    <strong>•</strong> {{ $machine->alarm->position_change_alarm }}</br>
                @endif
                @if($machine->alarm->service_alarm_status)
                    <strong>•</strong> {{ $machine->alarm->service_alarm_status }}</br>
                @endif
                @if($machine->alarm->sterilization_alarm)
                    <strong>•</strong> {{ $machine->alarm->sterilization_alarm }}</br>
                @endif
                @if($machine->alarm->filter_alarm)
                    <strong>•</strong> {{ $machine->alarm->filter_alarm }}</br>
                @endif
                @if($machine->alarm->water_shortage_alarm)
                    <strong>•</strong> {{ $machine->alarm->water_shortage_alarm }}</br>
                @endif
                @if($machine->alarm->filter_anti_counterfeiting_alarm)
                    <strong>•</strong> {{ $machine->alarm->filter_anti_counterfeiting_alarm }}</br>
                @endif
                @if($machine->alarm->slave_mobile_alarm)
                    <strong>•</strong> {{ $machine->alarm->slave_mobile_alarm }}</br>
                @endif
                @if($machine->alarm->dehumidification_tank_full_water_alarm)
                    <strong>•</strong> {{ $machine->alarm->dehumidification_tank_full_water_alarm }}</br>
                @endif
                @if($machine->alarm->malfunction_code)
                    <strong>•</strong> 设备故障代码:{{ $machine->alarm->malfunction_code }}<a href="javacript:;" id="malfunction_code">「故障代码表」</a>
                @endif
            </div>
            @endif
            <form method="get" class="form-horizontal">
                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('admin/machine.status') }}{{ __('admin/machine.explanation') }}</h5></div>
                        <a href="javascript:;" id="status" class="btn btn-normal btn-m" onclick="refresh('status', '{{ $machine->device }}')">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('admin/machine.app_version') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->app_version }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.firmware_version') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->firmware_version }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.current_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.2g4g_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->g_status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.wifi_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->wifi_status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.bluetooth_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->bluetooth_status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.overage') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                热水：{{ $machine->hot_water_overage }} ml
                                凉水：{{ $machine->cold_water_overage }} ml
                                氧气：{{ secToMin($machine->oxygen_overage) }} min
                                空净：{{ secToMin($machine->air_overage) }} min
                                加湿：{{ secToMin($machine->humidity_add_overage) }} min
                                除湿：{{ secToMin($machine->humidity_minus_overage) }} min
                                儿童恒湿：{{ secToMin($machine->humidity_child_overage) }} min
                                成人恒湿：{{ secToMin($machine->humidity_adult_overage) }} min
                            </div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.sterilizations') }}(h)</label>
                            <div class="col-md-9 control-label t-a-l">
                                uv1:{{ secToHR($machine->sterilization->uv1) }}
                                uv2:{{ secToHR($machine->sterilization->uv2) }}
                                uv3:{{ secToHR($machine->sterilization->uv3) }}
                                uv4:{{ secToHR($machine->sterilization->uv4) }}
                                uv5:{{ secToHR($machine->sterilization->uv5) }}
                                uv6:{{ secToHR($machine->sterilization->uv6) }}
                            </div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.filter1_lifespan') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->filter1_lifespan }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.filter2_lifespan') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->filter2_lifespan }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.filter3_lifespan') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->filter3_lifespan }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.total_produce_water_time') }}(h)</label>
                            <div class="col-md-9 control-label t-a-l">{{ secToHR($machine->total_produce_water_time) }}</div>
                        </div>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('admin/machine.environment') }}{{ __('admin/machine.explanation') }}</h5></div>
                        <a href="javascript:;" id="environment" class="btn btn-normal btn-m" onclick="refresh('environment', '{{ $machine->device }}')">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('admin/machine.temperature') }}(℃)</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->temperature }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.humidity') }}(%RH)</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->humidity }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.pm2_5') }}(mg/m3)</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->pm2_5 }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.oxygen_concentration') }}(%)</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->oxygen_concentration }}</div>
                        </div>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('admin/machine.water_quality') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="playlist-list">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.raw_water') }}(ppm)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->waterQualityStatistics->last() ? $machine->waterQualityStatistics->last()->raw_water_tds : 0 }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.pure_water') }}(ppm)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->waterQualityStatistics->last() ? $machine->waterQualityStatistics->last()->pure_water_tds : 0 }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.salt_rejection_rate') }}(%)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->waterQualityStatistics->last() ? $machine->waterQualityStatistics->last()->salt_rejection_rate : 0 }}</div>
                                    </td>
                                    <td class="playlist-actions pl">
                                        <a href="{{ route('admin.machine.water_quality_statistics', ['machine' => $machine->id]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.water_quality_statistics') }}</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('admin/machine.records') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="playlist-list">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.bluetooth') }}</label>
                                        <div class="col-md-9 control-label t-a-l">#</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_connect_times') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->bluetoothRecords->count() }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_connect_time') }}(h)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ secToHR($machine->bluetoothRecords->sum('total_time')) }}</div>
                                    </td>
                                    <td class="playlist-actions pl">
                                        <a href="{{ route('admin.machine.bluetooth.records', ['machine' => $machine->id]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.bluetooth_records') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.water') }}</label>
                                        <div class="col-md-9 control-label t-a-l">#</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_water') }}(ml)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->waterRecords->sum('total_flow') }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_water_time') }}(h)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ secToHR($machine->waterRecords->sum('time')) }}</div>
                                    </td>
                                    <td class="playlist-actions pl">
                                        <a href="{{ route('admin.machine.water.records', ['machine' => $machine->id]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.water_records') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.air') }}</label>
                                        <div class="col-md-9 control-label t-a-l">#</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_air_time') }}(h)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ secToHR($machine->airRecords->sum('time')) }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l"></label>
                                        <div class="col-md-9 control-label t-a-l"></div>
                                    </td>
                                    <td class="playlist-actions pl">
                                        <a href="{{ route('admin.machine.air.records', ['machine' => $machine->id]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.air_records') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.oxygen') }}</label>
                                        <div class="col-md-9 control-label t-a-l">#</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_oxygen_time') }}(l)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->oxygenRecords->sum('time') }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l"></label>
                                        <div class="col-md-9 control-label t-a-l"></div>
                                    </td>
                                    <td class="playlist-actions pl">
                                        <a href="{{ route('admin.machine.oxygen.records', ['machine' => $machine->id]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.oxygen_records') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.humidity') }}</label>
                                        <div class="col-md-9 control-label t-a-l">#</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_humidity_time') }}(h)</label>
                                        <div class="col-md-9 control-label t-a-l">{{ secToHR($machine->humidityRecords->sum('time')) }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l"></label>
                                        <div class="col-md-9 control-label t-a-l"></div>
                                    </td>
                                    <td class="playlist-actions pl">
                                        <a href="{{ route('admin.machine.humidity.records', ['machine' => $machine->id]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.humidity_records') }}</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
    <div class="show" style="display: none;">
        <div class="overlay"></div>
        <div class="img-show">
            <span><i class="fa fa-close"></i></span>
            <img src="/images/superadmin/code.jpeg">
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    function refresh(id, device) {
        $("#"+id).html("<i class='fa fa-refresh fa-spin'></i>");
        if (id == 'status') {
            var url = "/api/push_hardware_status_signal";
        } else {
            var url = "/api/push_environment_signal";
        }
        $.ajax({
            type: "get",
            async : true,
            url: url,
            data: {
                'device' : device
            },
            dataType: "json",
            success: function(result){
                if (result.http_code == 200) {
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    refreshed(id);
                    alert(result.msg)
                }
            },
            error: function(errmsg) {
                refreshed(id);
                console.log("Ajax获取服务器数据出错了！"+ errmsg.status + ' ' + errmsg.statusText);
            }
        });
    }
    function refreshed(id) {
        $("#"+id).html("<i class='fa fa-refresh' aria-hidden='true'></i>");
    }


    $("#malfunction_code").click(function () {
        $(".show").fadeIn();
    });
    
    $("span, .overlay").click(function () {
        $(".show").fadeOut();
    });
</script>
@endpush