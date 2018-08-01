@extends('admin.layout')

@push('title')
设备列表
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.machines') }}</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.machines') }}</strong></p>
        </div><!-- .col-* -->

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>{{ __('admin/machine.mac') }}</th>
                            <th>{{ __('admin/machine.hot_water_remaining_time') }}(min)</th>
                            <th>{{ __('admin/machine.cold_water_remaining_time') }}(min)</th>
                            <th>{{ __('admin/machine.air_remaining_time') }}(min)</th>
                            <th>{{ __('admin/machine.oxygen_remaining_time') }}(min)</th>
                            <th>{{ __('admin/machine.humidity_remaining_time') }}(min)</th>
                            <th>{{ __('admin/machine.alarm_status') }}</th>
                            <th>{{ __('admin/machine.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($machines as $machine)
                                <tr>
                                    <td>{{ $machine->device }}</td>
                                    <td>{{ secToMin($machine->hot_water_overage) }}</td>
                                    <td>{{ secToMin($machine->cold_water_overage) }}</td>
                                    <td>{{ secToMin($machine->air_overage) }}</td>
                                    <td>{{ secToMin($machine->oxygen_overage) }}</td>
                                    <td>{{ secToMin($machine->humidity_overage) }}</td>
                                    <td>
                                        @if($machine->hasAlarms())
                                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="playlist-actions hp">
                                        <a href="javascript:;" id="machine-{{ $machine->id }}" class="btn btn-normal btn-m" title="刷新余量" onclick="refresh('{{ $machine->id }}', '{{ $machine->registration_id }}')">
                                            <i class="fa fa-refresh" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('admin.machine.show', $machine->id) }}" class="btn btn-normal btn-m" title="详情">
                                            <i class="fa fa-info"></i>
                                        </a>
                                        <a href="{{ route('admin.machine.debug', $machine->id) }}" class="btn btn-normal btn-m" title="调试信息">
                                            <i class="fa fa-book"></i>
                                        </a>
                                        <a href="{{ route('admin.machine.clean_overage', $machine->id) }}" class="btn btn-normal btn-m" title="清除余量">
                                            <i class="fa fa-eraser"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="row">
                        <div class="col-5">
                            <div class="dataTables_info">
                                @if ($machines->count()>0)
                                {{
                                        __(
                                            'admin/machine.showing_from_to_machines',
                                            [
                                                'from'=>$machines->firstItem(),
                                                'to'=>$machines->lastItem(),
                                                'total'=>$machines->total()
                                            ]
                                        )
                                    }}
                                @else
                                    {{ __('admin/machine.no_machines') }}
                                @endif
                            </div>
                        </div>

                        <div class="col-7">
                            <div class="dataTables_paginate paging_simple_numbers">
                                {{ $machines->appends(request()->input())->links() }}
                            </div>
                        </div>
                    </div>

                </div><!-- .ibox-content -->

            </div><!-- .ibox -->

        </div><!-- .col* -->

    </div><!-- .row -->
@stop

@push('js')
<script>
    function refresh(id, registrationId) {
        $("#machine-"+id).html("<i class='fa fa-refresh fa-spin'></i>");
        $.ajax({
            type: "get",
            async : true,
            url: "/api/push_overage_signal",
            data: {
                'registrationId' : registrationId
            },
            dataType: "json",
            success: function(result){
                if (result.http_code == 200) {
                    getReportStatus(result.body.msg_id, registrationId, id);
                }
            },
            error: function(errmsg) {
                console.log("Ajax获取服务器数据出错了！"+ errmsg);
            }
        });
    }

    function getReportStatus(msgId, registrationId, id) {
        $.ajax({
            type: "get",
            async : true,
            url: "/api/get_report_status",
            data: {
                'msg_id' : msgId,
                'registration_ids' : registrationId,
            },
            dataType: 'json',
            success: function(result){
                console.log(result);
                if (result.registrationId.status == 0) {
                    location.reload();
                } else {
                    refreshed(id);
                    alert('机器未在线，获取各余量失败！')
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("Ajax获取服务器数据出错了！"+ errorThrown);
            }
        });
    }
    
    function refreshed(id) {
        $("#machine-"+id).html("<i class='fa fa-refresh' aria-hidden='true'></i>");
    }
</script>
@endpush