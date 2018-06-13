@extends('admin.layout')
@push('title')
编辑管理员
@endpush

@section('content')

    <div class="row justify-content-center">

            <header class="title-header col-md-9">
                <h3 class="title">{{ __('admin/user.edit_user') }}</h3>
            </header>

            <div class="col-md-9">
                <a href="{{ route('admin.user.index') }}" class="btn btn-normal btn-m">{{ __('admin/user.back') }}</a>
                <p class="brdcrmb"><strong class="brdcrmb-item">{{ __('admin/user.edit_user') }}</strong></p>
            </div><!-- .col-* -->

            <div class="col-md-9">

                {{ Form::open(['url' => route('admin.user.update', $user->id), 'method' => 'POST', 'name'=>'form_user_creation', 'class' => 'form-horizontal']) }}
                {{ method_field('PUT') }}
                <div class="ibox">

                    <div class="ibox-title">
                        <h5>{{ __('admin/user.user_information') }}</h5>
                    </div>

                    <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('admin/user.name') }}</label>
                                <div class="col-md-9">
                                    <input type="name" name="name" value="{{ $user->name }}" class="form-control " readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('admin/user.email') }}</label>
                                <div class="col-md-9">
                                    <input type="email" name="email" value="{{ $user->email }}" class="form-control " readonly>
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('password') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/user.password') }}*</label>
                                <div class="col-md-9">
                                    <input type="password" name="password" class="form-control">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'password'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('password_confirmation') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/user.re_type_password') }}*</label>
                                <div class="col-md-9">
                                    <input type="password" name="password_confirmation" class="form-control">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'password_confirmation'])
                                </div>
                            </div>

                    </div><!-- .ibox-content -->

                </div><!-- .ibox -->

                <div class="form-save">
                    {{ Form::submit(__('admin/user.update'), ['class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}
            </div><!-- .col* -->

        </div><!-- .row -->

@stop
