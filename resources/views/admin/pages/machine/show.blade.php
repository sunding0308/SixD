@extends('admin.layout')

@push('title')
设备详情
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
        <div class="form">
            <div class="title-header">
                <a href="{{ route('admin.machine.index') }}" class="btn btn-normal btn-m">{{ __('admin/machine.back') }}</a>
                <div class="title">{{ __('admin/machine.detail') }}</div>
            </div>
            <form method="get" class="form-horizontal">
                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('admin/machine.status') }}{{ __('admin/machine.explanation') }}</h5></div>
                        <a href="javascript:;" id="status" class="btn btn-normal btn-m" onclick="refresh('status')">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('admin/machine.current_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.2g4g_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->g_status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.wifi_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->wifi_status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.bluetooth_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->bluetooth_status }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.overage') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->overage }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.sterilizations') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                uv1:{{ $machine->sterilization->uv1 }}
                                uv2:{{ $machine->sterilization->uv2 }}
                                uv3:{{ $machine->sterilization->uv3 }}
                                uv4:{{ $machine->sterilization->uv4 }}
                                uv5:{{ $machine->sterilization->uv5 }}
                                uv6:{{ $machine->sterilization->uv6 }}
                            </div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.filter1_lifespan') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->filter1_lifespan }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.filter2_lifespan') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->filter2_lifespan }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.filter3_lifespan') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->filter3_lifespan }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.total_produce_water_time') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->total_produce_water_time }}</div>
                        </div>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('admin/machine.environment') }}{{ __('admin/machine.explanation') }}</h5></div>
                        <a href="javascript:;" id="environment" class="btn btn-normal btn-m" onclick="refresh('environment')">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('admin/machine.temperature') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->temperature }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.humidity') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->humidity }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.pm2_5') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->pm2_5 }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.oxygen_concentration') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->oxygen_concentration }}</div>
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
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_connect_time') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->bluetoothRecords->sum('total_time') }}</div>
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
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_water') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->waterRecords->sum('total_flow') }}</div>
                                    </td>
                                    <td>
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_water_time') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->waterRecords->sum('time') }}</div>
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
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_air_time') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->airRecords->sum('time') }}</div>
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
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_oxygen_time') }}</label>
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
                                        <label class="col-md-9 control-label t-a-l">{{ __('admin/machine.total_humidity_time') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ $machine->humidityRecords->sum('time') }}</div>
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
</div>
@stop

@push('js')
<script>
    function refresh(id) {
        $("#"+id).html("<i class='fa fa-refresh fa-spin'></i>");
    }
    function refreshed(id) {
        $("#"+id).html("<i class='fa fa-refresh' aria-hidden='true'></i>");
    }
</script>
@endpush