@extends('admin.layout')

@push('title')
微售货机列表
@endpush

@section('content')
    <div class="row">

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">{{ __( 'admin/machine.vendings') }}</strong></a></p>
        </div><!-- .col-* -->

        <div class="col-md-12">
            <div class="title-header ">
                <div class="min-menu row">
                    <div class="col-md-3">
                        <div class="title">{{ __( 'admin/machine.vendings') }}</div>
                    </div>
                    <div class="col-md-9">
                        <div class="right-panel">
                            {!! Form::open(['url'=>route('admin.machine.index'),'method'=>'GET']) !!}
                            <div class="status-float">
                                <a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_VENDING, 'alarm' => 'true', 'hotel' => $hotel, 'room' => $room]) }}" class="btn btn-normal m-r">{{ __('admin/machine.alarming') }} <span class="label label-license">{{ $machines->sum('alarm_count') }}</span></a>
                            </div>
                            <div class="status-float">
                                <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('admin/machine.hotel_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                    @foreach($hotelNames as $hotelName)
                                        <li><a href="{{ url()->current(). '?type='.\App\Machine::TYPE_VENDING.'&alarm='.$alarm.'&hotel='.$hotelName.'&room='.$room }}">{{ $hotelName }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="input-group">
                                @foreach(request()->except('room') as $key=>$value)
                                    {!! Form::hidden($key, $value) !!}
                                @endforeach
                                <input type="text" placeholder="{{ __('admin/machine.search_room') }}" class="input-sm form-control" name="room" value="{{ request()->query('room') }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </span>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="col-md-12 search-result">
                        @if($hotel)
                            {{ __('admin/machine.hotel') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $hotel }} </span><span class="search-close"><a href="{{ url()->current(). '?type='.\App\Machine::TYPE_VENDING.'&alarm='.$alarm.'&room='.$room }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                        @endif
                        @if($room)
                            {{ __('admin/machine.room') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $room }} </span><span class="search-close"><a href="{{ url()->current(). '?type='.\App\Machine::TYPE_VENDING.'&alarm='.$alarm.'&hotel='.$hotel }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                        @endif
                        @if($alarm)
                            {{ __('admin/machine.status') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ __('admin/machine.alarming') }} </span><span class="search-close"><a href="{{ url()->current(). '?type='.\App\Machine::TYPE_VENDING.'&hotel='.$hotel.'&room='.$room }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>{{ __('admin/machine.hotel_name') }}</th>
                            <th>{{ __('admin/machine.room_no') }}</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>{{ __('admin/machine.device_status') }}</th>
                            <th>{{ __('admin/machine.alarm_status') }}</th>
                            <th>{{ __('admin/machine.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($machines as $machine)
                                <tr>
                                    <td @if(!$machine->machine_id)style="color:red"@endif>{{ $machine->installation ? $machine->installation->hotel_name : '-' }}</td>
                                    <td @if(!$machine->machine_id)style="color:red"@endif>{{ $machine->installation ? $machine->installation->room : '-' }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[0]->quantity : 0 }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[1]->quantity : 0 }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[2]->quantity : 0 }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[3]->quantity : 0 }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[4]->quantity : 0 }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[5]->quantity : 0 }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[6]->quantity : 0 }}</td>
                                    <td>{{ count($machine->stocks) ? $machine->stocks[7]->quantity : 0 }}</td>
                                    <td>
                                        <span class="{{ $machineStatus[$machine->device]->Status == 'ONLINE'?'label-normal':'label-danger' }}">
                                        {{ __('admin/machine.device_'.strtolower($machineStatus[$machine->device]->Status)) }}
                                        </span>
                                    </td>
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
                                        <a href="{{ route('admin.machine.show', ['machine' => $machine->id]) }}" class="btn btn-normal btn-m" title="详情">
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