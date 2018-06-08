@if (count($errors))
    <div class="alert alert-danger">
        <strong>Oops!</strong> There is an error with your submission.
    </div>
@elseif (session()->exists('success'))
    <div class="alert alert-success" role="alert">
        <strong>Nice!</strong> {!! Session::pull('success') !!}
    </div>
@elseif (session()->exists('warning'))
    <div class="alert alert-warning" role="alert">
        {!! Session::pull('warning') !!}
    </div>
@elseif (session()->exists('error'))
    <div class="alert alert-danger" role="alert">
        <strong>Oops!</strong> {!! Session::pull('error') !!}
    </div>
@elseif (session()->exists('well_done'))
    <div class="errors p-a-30">
        <div class="alert alert-success" role="alert">
            <strong>{{ trans('signup.well_done') }}</strong> {{ trans('signup.successful_activate') }}
        </div>
    </div>
@elseif(session()->exists('invalid_token'))
    <div class="errors p-a-30">
        <div class="alert alert-danger" role="alert">
            <strong>{{ trans('signup.oops') }}</strong> {{ trans('signup.invalid_token') }}
        </div>
    </div>
@endif
