@extends('admin.layout')

@push('title')
微售货机列表
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.vendings') }}</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.vendings') }}</strong></a></p>
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
                            @foreach($machines as $machine)
                                <tr>
                                    <td @if(!$machine->machine_id)style="color:red"@endif>{{ $machine->device }}</td>
                                    <td>{{ $machine->stocks[0]->quantity }}</td>
                                    <td>{{ $machine->stocks[1]->quantity }}</td>
                                    <td>{{ $machine->stocks[2]->quantity }}</td>
                                    <td>{{ $machine->stocks[3]->quantity }}</td>
                                    <td>{{ $machine->stocks[4]->quantity }}</td>
                                    <td>{{ $machine->stocks[5]->quantity }}</td>
                                    <td>{{ $machine->stocks[6]->quantity }}</td>
                                    <td>{{ $machine->stocks[7]->quantity }}</td>
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
                                        @else
                                        <a href="{{ route('admin.machine.show', ['machine' => $machine->id, 'type' => \App\Machine::TYPE_VENDING]) }}" class="btn btn-normal btn-m" title="详情">
                                            <i class="fa fa-info"></i>
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