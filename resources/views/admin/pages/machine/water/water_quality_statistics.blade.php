@extends('admin.layout')

@push('title')
水质变化统计
@endpush
@push('head-scripts')
<script src="https://cdn.bootcss.com/echarts/4.1.0.rc2/echarts-en.common.js"></script>
@endpush

@section('content')
    <div class="row">
        <header class="title-header col-md-12">
            <h3 class="title">统计</h3>
            <div class="left-panel">
                <select class="form-control sort-position" onchange="filterDate(this.value)">
                    <option value="one_month">最近一个月</option>
                    <option value="three_month">最近三个月</option>
                    <option value="one_year">最近一年</option>
                </select>
            </div>
            <div class="right-panel">
                <a href="{{ route('admin.machine.show', $machine->id) }}" class="btn btn-normal btn-m m-r">{{ __('admin/machine.back') }}</a>
            </div>
        </header>
        <!-- 为 ECharts 准备一个具备大小（宽高）的 DOM -->
        <div id="water_tds" style="width:1000px;height:400px;margin-top:40px"></div>
        <div id="salt_rejection_rate" style="width:1000px;height:400px;margin-top:40px"></div>
    </div><!-- .row -->

    <input type="hidden" name="machine_id" value="{{ $machine->id }}">
@stop

@push('js')
<script src="/js/statistics.js"></script>
@endpush