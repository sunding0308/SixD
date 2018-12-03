@extends('admin.layout')

@push('title')
杀菌马桶盖列表
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.toilet_lid') }}</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.toilet_lid') }}</strong></a></p>
        </div><!-- .col-* -->

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>{{ __('admin/machine.id') }}</th>
                            <th>{{ __('admin/machine.2g_status') }}</th>
                            <th>{{ __('admin/machine.firmware_version') }}</th>
                            <th>{{ __('admin/machine.uesd_time') }}(min)</th>
                            <th>{{ __('admin/machine.remaining_time') }}(min)</th>
                            <th>{{ __('admin/machine.alarm_status') }}</th>
                            <th>{{ __('admin/machine.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($machines as $machine)
                            <tr>
                                <td @if(!$machine->machine_id)style="color:red"@endif>{{ $machine->device }}</td>
                                <td>{{ $machine->g_status ?: '-' }}</td>
                                <td>{{ $machine->firmware_version ?: '-' }}</td>
                                <td>{{ $machine->toiletLidTime->used_time }}</td>
                                <td>{{ $machine->toiletLidTime->remain_time }}</td>
                                <td>
                                    @if($machine->hasAlarms())
                                        <i class="fa fa-exclamation-triangle fa-2x"></i>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="playlist-actions hp">
                                    @if(!$machine->machine_id)
                                    未安装
                                    <a href="{{ route('admin.machine.debug', $machine->id) }}" class="btn btn-normal btn-m" title="调试信息">
                                        <i class="fa fa-book"></i>
                                    </a>
                                    @else
                                    <a href="javascript:;" id="machine-{{ $machine->id }}" class="btn btn-normal btn-m" title="刷新余量" onclick="refresh('{{ $machine->id }}', '{{ $machine->device }}')">
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('admin.machine.debug', $machine->id) }}" class="btn btn-normal btn-m" title="调试信息">
                                        <i class="fa fa-book"></i>
                                    </a>
                                    <a href="{{ route('admin.machine.clean_overage', $machine->id) }}" class="btn btn-normal btn-m" title="清除余量">
                                        <i class="fa fa-eraser"></i>
                                    </a>
                                    @endif
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
    function refresh(id, device) {
        $("#machine-"+id).html("<i class='fa fa-refresh fa-spin'></i>");
        $.ajax({
            type: "get",
            async : true,
            url: "/api/push_overage_signal",
            data: {
                'device' : device
            },
            dataType: "json",
            success: function(result){
                // console.log(result.body[registrationId].status);
                // console.log(result.http_code);
                if (result.http_code == 200) {
                    setTimeout(() => {
                        location.reload();
                    }, 5000);
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
        $("#machine-"+id).html("<i class='fa fa-refresh' aria-hidden='true'></i>");
    }
</script>
@endpush