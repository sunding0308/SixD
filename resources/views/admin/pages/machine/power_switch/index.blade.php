@extends('admin.layout')

@push('title')
物联开关列表
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.power_switch') }}</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.power_switch') }}</strong></a></p>
        </div><!-- .col-* -->

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>{{ __('admin/machine.id') }}</th>
                            <th>打开于</th>
                            <th>关闭于</th>
                            <th>{{ __('admin/machine.device_status') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($machines as $machine)
                            <tr>
                                <td @if(!$machine->machine_id)style="color:red"@endif>{{ $machine->device }}</td>
                                <td>{{ optional($machine->data)->enabled_at }}</td>
                                <td>{{ optional($machine->data
                                )->disabled_at }}</td>
                                <td>
                                    @if (array_key_exists($machine->device, $machineStatus))
                                        <span class="{{ $machineStatus[$machine->device]->Status == 'ONLINE'?'label-normal':'label-danger' }}">
                                        {{ __('admin/machine.device_'.strtolower($machineStatus[$machine->device]->Status)) }}
                                        </span>
                                    @else
                                        <span class="label-danger">
                                        {{ __('admin/machine.device_offline') }}
                                        </span>
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