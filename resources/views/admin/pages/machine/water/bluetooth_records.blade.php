@extends('admin.layout')

@push('title')
蓝牙音箱使用记录
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.bluetooth_records') }}</h3>
            <div class="right-panel">
                <a href="{{ route('admin.machine.show', $machine->id) }}" class="btn btn-normal btn-m m-r">{{ __('admin/machine.back') }}</a>
            </div>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.bluetooth_records') }}</strong></p>
        </div><!-- .col-* -->

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th style="width: 33%;">{{ __('admin/machine.started_at') }}</th>
                            <th style="width: 33%;">{{ __('admin/machine.stopped_at') }}</th>
                            <th style="width: 0.5%;"></th>
                            <th style="width: 0.5%;"></th>
                            <th style="width: 33%;">{{ __('admin/machine.connect_time') }}(min)</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($bluetoothRecords as $bluetoothRecord)
                                <tr>
                                    <td>{{ $bluetoothRecord->started_at }}</td>
                                    <td>{{ $bluetoothRecord->stopped_at }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ secToMin($bluetoothRecord->total_time) }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="row">
                        <div class="col-5">
                            <div class="dataTables_info">
                                @if ($bluetoothRecords->count()>0)
                                {{
                                        __(
                                            'admin/machine.showing_from_to_bluetooth_records',
                                            [
                                                'from'=>$bluetoothRecords->firstItem(),
                                                'to'=>$bluetoothRecords->lastItem(),
                                                'total'=>$bluetoothRecords->total()
                                            ]
                                        )
                                    }}
                                @else
                                    {{ __('admin/machine.no_records') }}
                                @endif
                            </div>
                        </div>

                        <div class="col-7">
                            <div class="dataTables_paginate paging_simple_numbers">
                                {{ $bluetoothRecords->appends(request()->input())->links() }}
                            </div>
                        </div>
                    </div>

                </div><!-- .ibox-content -->

            </div><!-- .ibox -->

        </div><!-- .col* -->

    </div><!-- .row -->
@stop