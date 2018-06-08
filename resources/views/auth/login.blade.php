@extends('partials.layout_min')

@push('title')
管理后台登录
@endpush

@section('body')
    <div class="middle-box text-center loginscreen">
        <div>
            <div>
               <img src="/images/logo.jpeg">
            </div>
            <h3>后台登录</h3>
            @if (session()->exists('well_done'))
                <div class="errors p-a-30">
                    <div class="alert alert-success" role="alert">
                        <strong>{{ trans('signup.well_done') }}</strong> {{ trans('signup.successful_activate') }}
                    </div>
                </div>
            @endif
            @if (session()->exists('invalid_token'))
                <div class="errors p-a-30">
                    <div class="alert alert-danger" role="alert">
                        <strong>Oops!</strong> {{ trans('signup.invalid_token') }}
                    </div>
                </div>
            @endif
            @if (session()->exists('error'))
                <div class="errors p-a-30">
                    <div class="alert alert-danger" role="alert">
                        <strong>Oops!</strong> {{ trans('signup.error') }}
                    </div>
                </div>
            @endif
            @if (session()->exists('logout'))
                <div class="errors p-a-30">
                    <div class="alert alert-success" role="alert">
                        {!! Session::pull('logout') !!}
                    </div>
                </div>
            @endif
            @if(!session()->exists('invalid_token') && ($errors->has('email') || $errors->has('inactive')))
                <div class="alert alert-danger">
                    @if($errors->has('email'))
                            <p> <strong>诶呀!</strong> {{ $errors->first('email') }}</p>
                    @endif
                    @if ($errors->has('inactive'))
                            <a href="{{url('activate_password/email/'.$errors->first('inactive'))}}">{{ trans('auth.inactive') }}</a>
                    @endif
                </div>
            @endif

            {{ Form::open(['method'=>'POST', 'url'=>url('login'), 'class'=>'m-t']) }}

                <div class="form-group{{ !session()->exists('invalid_token') && $errors->has('email') ? " has-danger" : "" }}">
                    <input type="email" name="email" class="form-control{{ !session()->exists('invalid_token') && $errors->has('email') ? " form-control-danger" : "" }}" placeholder="邮箱地址" required="" value="{{ old('email') }}">
                </div>

                <div class="form-group{{ $errors->has('password') ? " has-danger" : "" }}">
                    <input type="password" name="password" class="form-control{{ $errors->has('email') ? " form-control-danger" : "" }}" placeholder="密码" required="">
                    @include('partials.errors', ['err_type' => 'field','field' => 'password'])
                </div>

                <div class="form-check">
                        <div class="form-check-label"><label><input name="remember" type="checkbox" class="form-check-input"><i></i> 记住我</label></div>
                </div>

                <button type="submit" class="btn btn-primary block full-width m-b">登录</button>
            {{ Form::close() }}

                <a href="{{ url('/password/reset') }}"><small>忘记密码?</small></a>
                <!--<p class="text-muted text-center"><small>Don't have an account?</small></p>
                <a class="btn btn-normal btn-m register" href="{{ url('signup') }}">Create an account</a>-->

            <p class="m-t"> <small>© 2018 6D. 版权所有.</small> </p>
        </div>
    </div>
@stop
