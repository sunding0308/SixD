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
                <a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_RELENISHMENT]) }}" class="btn btn-normal btn-m">{{ __('admin/machine.back') }}</a>
                <div class="title">{{ __('admin/machine.detail') }}</div>
            </div>
            <div class="alert alert-detail alert-danger" role="alert">
                八马铁观音缺货
            </div>
            <form method="get" class="form-horizontal">
                <div class="ibox">
                    <div class="ibox-title title-with-button">
                        <div class="title-button-header"><h5>{{ __('admin/machine.status') }}{{ __('admin/machine.explanation') }}</h5></div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('admin/machine.firmware_version') }}</label>
                            <div class="col-md-9 control-label t-a-l">-</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.current_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">当前正处于正常状态</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.2g_status') }}</label>
                            <div class="col-md-9 control-label t-a-l">信号强</div>
                            <label class="col-md-3 control-label">{{ __('admin/machine.overage') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                海飞丝洗发露：2 包
                                潘婷洗发露：2 包
                                隆力奇沐浴露：2 包
                                亮莊沐浴露：1 包
                                美式浓缩咖啡：2 包
                                雀巢咖啡：2 包
                                立顿红茶：2 包
                                立顿绿茶：0 包
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