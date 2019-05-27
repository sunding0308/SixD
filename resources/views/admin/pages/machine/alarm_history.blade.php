@extends('admin.layout')

@push('title')
{{ __('admin/machine.alarm_history') }}
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
        <div class="form">
            <div class="title-header">
                <a href="{{ route('admin.machine.show', $machine->id) }}" class="btn btn-normal btn-m">{{ __('admin/machine.back') }}</a>
                <div class="title">{{ __('admin/machine.alarm_history') }}</div>
            </div>
            <form class="form-horizontal">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 50%;">{{ __('admin/machine.date') }}</th>
                                    <th style="width: 25%;">{{ __('admin/machine.alarm_type') }}</th>
                                    <th style="width: 25%;">{{ __('admin/machine.alarm_status') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($alarmHistories as $history)
                                    <tr>
                                        <td>{{ $history->created_at }}</td>
                                        <td>{{ strtoupper($history->malfunction_code) }}</td>
                                        <td>
                                        <span class="{{ $history->cleared?'label-success':'label-danger' }}">
                                            {{ $history->cleared? __('admin/machine.alarm_cleared'):__('admin/machine.alarm_created')}}
                                        </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-5">
                                    <div class="dataTables_info">
                                        @if ($alarmHistories->count()>0)
                                            {{
                                                __(
                                                    'admin/machine.showing_from_to_water_records',
                                                    [
                                                        'from'=>$alarmHistories->firstItem(),
                                                        'to'=>$alarmHistories->lastItem(),
                                                        'total'=>$alarmHistories->total()
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
                                        {{ $alarmHistories->appends(request()->input())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
@stop

