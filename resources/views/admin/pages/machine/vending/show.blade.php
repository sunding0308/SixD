@extends('admin.layout')

@push('title')
微售货机详情
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
        <div class="form">
            <div class="title-header">
                <a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_VENDING]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.back') }}</a>
                <div class="title">{{ __('admin/machine.detail') }}</div>
            </div>
            @if($machine->hasAlarms())
            <div class="alert alert-detail alert-danger" role="alert">
                @if($machine->alarm->malfunction_code)
                    <strong>•</strong> 设备故障代码:{{ $machine->alarm->malfunction_code }}<a href="javacript:;" id="malfunction_code">「故障代码表」</a>
                @endif
            </div>
            @endif
            <form method="get" class="form-horizontal">
                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('admin/machine.status') }}{{ __('admin/machine.explanation') }}</h5></div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('admin/machine.firmware_version') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->firmware_version ?: '-' }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.current_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->status ?: '-' }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.2g_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $machine->g_status ?: '-' }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.stock') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                海飞丝洗发露：{{ $machine->stocks[0]->quantity }} 包<br>
                                潘婷洗发露：{{ $machine->stocks[1]->quantity }} 包<br>
                                隆力奇沐浴露：{{ $machine->stocks[2]->quantity }} 包<br>
                                亮莊沐浴露：{{ $machine->stocks[3]->quantity }} 包<br>
                                美式浓缩咖啡：{{ $machine->stocks[4]->quantity }} 包<br>
                                雀巢咖啡：{{ $machine->stocks[5]->quantity }} 包<br>
                                立顿红茶：{{ $machine->stocks[6]->quantity }} 包<br>
                                立顿绿茶：{{ $machine->stocks[7]->quantity }} 包<br>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
    <div class="show">
        <div class="overlay"></div>
        <div class="img-show">
            <span><i class="fa fa-close"></i></span>
            <img src="/images/superadmin/code.jpeg">
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    $("#malfunction_code").click(function () {
        $(".show").fadeIn();
    });
    
    $("span, .overlay").click(function () {
        $(".show").fadeOut();
    });
</script>
@endpush