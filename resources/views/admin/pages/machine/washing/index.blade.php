@extends('admin.layout')

@push('title')
清洗机列表
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.washings') }}</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.washings') }}</strong></a></p>
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
                            <th>{{ __('admin/machine.washing_remaining_time') }}(min)</th>
                            <th>{{ __('admin/machine.alarm_status') }}</th>
                            <th>{{ __('admin/machine.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>35 931406 923302 3</td>
                                <td>信号强</td>
                                <td>-</td>
                                <td>21600</td>
                                <td>
                                    -
                                </td>
                                <td class="playlist-actions hp">
                                    <a href="javascript:;" id="machine-1" class="btn btn-normal btn-m" title="刷新余量" onclick="">
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                    </a>
                                    <a href="#" class="btn btn-normal btn-m" title="调试信息">
                                        <i class="fa fa-book"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>

                    </table>

                    <div class="row">
                        <div class="col-5">
                            <div class="dataTables_info">
                                共1个设备，当前1-1
                                {{-- @if ($machines->count()>0)
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
                                @endif --}}
                            </div>
                        </div>

                        {{-- <div class="col-7">
                            <div class="dataTables_paginate paging_simple_numbers">
                                {{ $machines->appends(request()->input())->links() }}
                            </div>
                        </div> --}}
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