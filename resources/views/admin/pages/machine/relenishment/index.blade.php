@extends('admin.layout')

@push('title')
补货柜列表
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.relenishments') }}</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.relenishments') }}</strong></a></p>
        </div><!-- .col-* -->

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>{{ __('admin/machine.id') }}</th>
                            <th>海飞丝洗发露(包)</th>
                            <th>潘婷洗发露(包)</th>
                            <th>隆力奇沐浴露(包)</th>
                            <th>亮莊沐浴露(包)</th>
                            <th>美式浓缩咖啡(包)</th>
                            <th>雀巢咖啡(包)</th>
                            <th>立顿红茶(包)</th>
                            <th>立顿绿茶(包)</th>
                            <th>{{ __('admin/machine.alarm_status') }}</th>
                            <th>{{ __('admin/machine.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>35 931406 923302 3</td>
                                <td>2</td>
                                <td>2</td>
                                <td>2</td>
                                <td>1</td>
                                <td>2</td>
                                <td>2</td>
                                <td>2</td>
                                <td>0</td>
                                <td>
                                    <i class="fa fa-exclamation-triangle fa-2x"></i>
                                </td>
                                <td class="playlist-actions hp">
                                    <a href="{{ route('admin.machine.show', [1, 'sign' => 809]) }}" class="btn btn-normal btn-m" title="详情">
                                        <i class="fa fa-info"></i>
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
                            共1个设备，当前1-1
                            {{-- <div class="dataTables_info">
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
                            </div> --}}
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