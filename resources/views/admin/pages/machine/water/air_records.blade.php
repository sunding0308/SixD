@extends('admin.layout')

@push('title')
空净使用记录
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __( 'admin/machine.air_records') }}</h3>
            <div class="right-panel">
                <a href="{{ route('admin.machine.show', $machine->id) }}" class="btn btn-normal btn-m m-r">{{ __('admin/machine.back') }}</a>
            </div>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.air_records') }}</strong></a></p>
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
                            <th style="width: 10%;"></th>
                            <th style="width: 10%;"></th>
                            <th style="width: 30%;">{{ __('admin/machine.air_time') }}(min)</th>
                            <th style="width: 10%;"></th>
                            <th style="width: 10%;"></th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($airRecords as $airRecord)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $airRecord->date }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ secToMin($airRecord->time) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="row">
                        <div class="col-5">
                            <div class="dataTables_info">
                                @if ($airRecords->count()>0)
                                {{
                                        __(
                                            'admin/machine.showing_from_to_air_records',
                                            [
                                                'from'=>$airRecords->firstItem(),
                                                'to'=>$airRecords->lastItem(),
                                                'total'=>$airRecords->total()
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
                                {{ $airRecords->appends(request()->input())->links() }}
                            </div>
                        </div>
                    </div>

                </div><!-- .ibox-content -->

            </div><!-- .ibox -->

        </div><!-- .col* -->

    </div><!-- .row -->
@stop