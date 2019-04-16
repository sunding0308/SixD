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
                    <strong>•</strong> 设备故障代码:{{ $machine->alarm->malfunction_code }} 电机故障
                @endif
            </div>
            @endif
            <form method="get" class="form-horizontal">
                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('admin/machine.status') }}</h5></div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('admin/machine.current_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $response['Success'] ? '在线' : '离线' }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.2g_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $response['Success'] ? $response['signal'] : '-' }}</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.stock') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                1：{{ $machine->stocks[0]->quantity }} 包<br>
                                2：{{ $machine->stocks[1]->quantity }} 包<br>
                                3：{{ $machine->stocks[2]->quantity }} 包<br>
                                4：{{ $machine->stocks[3]->quantity }} 包<br>
                                5：{{ $machine->stocks[4]->quantity }} 包<br>
                                6：{{ $machine->stocks[5]->quantity }} 包<br>
                                7：{{ $machine->stocks[6]->quantity }} 包<br>
                                8：{{ $machine->stocks[7]->quantity }} 包<br>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
    <div class="show" style="display: none;">
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