@extends('admin.layout')

@push('title')
水质变化统计
@endpush
@push('head-scripts')
<script src="https://cdn.bootcss.com/echarts/4.1.0.rc2/echarts-en.common.js"></script>
@endpush

@section('content')
    <div class="row">
        <select class="form-control sort-position" onchange="filterDate(this.value)">
            <option value="one_month">最近一个月</option>
            <option value="three_month">最近三个月</option>
            <option value="one_year">最近一年</option>
        </select>
        <!-- 为 ECharts 准备一个具备大小（宽高）的 DOM -->
        <div id="water_tds" style="width:1000px;height:400px;margin-top:40px"></div>
        <div id="salt_rejection_rate" style="width:1000px;height:400px;margin-top:40px"></div>
    </div><!-- .row -->

    <input type="hidden" name="machine_id" value="{{ $machine->id }}">
@stop

@push('js')
<script src="/js/statistics.js"></script>
@endpush