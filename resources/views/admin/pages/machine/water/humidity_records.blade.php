@extends('admin.layout')

@push('title')
湿度调节使用记录
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.humidity_records') }}</h3>
            <div class="right-panel">
                <a href="{{ route('admin.machine.show', $machine->id) }}" class="btn btn-normal btn-m m-r">{{ __('admin/machine.back') }}</a>
            </div>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.humidity_records') }}</strong></a></p>
        </div><!-- .col-* -->

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th style="width: 10%;"></th>
                            <th style="width: 10%;"></th>
                            <th style="width: 30%;">{{ __('admin/machine.date') }}</th>
                            <th style="width: 10%;">{{ __('admin/machine.type') }}</th>
                            <th style="width: 10%;"></th>
                            <th style="width: 30%;">{{ __('admin/machine.humidity_time') }}(min)</th>
                            <th style="width: 10%;"></th>
                            <th style="width: 10%;"></th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($humidityRecords as $humidityRecord)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $humidityRecord->date }}</td>
                                    <td>{{ $humidityRecord->type }}</td>
                                    <td></td>
                                    <td>{{ secToMin($humidityRecord->time) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="row">
                        <div class="col-5">
                            <div class="dataTables_info">
                                @if ($humidityRecords->count()>0)
                                {{
                                        __(
                                            'admin/machine.showing_from_to_humidity_records',
                                            [
                                                'from'=>$humidityRecords->firstItem(),
                                                'to'=>$humidityRecords->lastItem(),
                                                'total'=>$humidityRecords->total()
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
                                {{ $humidityRecords->appends(request()->input())->links() }}
                            </div>
                        </div>
                    </div>

                </div><!-- .ibox-content -->

            </div><!-- .ibox -->

        </div><!-- .col* -->

    </div><!-- .row -->
@stop