@if ($err_type == 'field')
	@if ($errors->has($field))
		@foreach ($errors->get($field) as $message)
			<p>{!! $message !!}</p>
		@endforeach
	@endif
@endif

@if ($err_type == 'header')
	@if (count($errors))
        <div class="alert alert-danger">
            <strong>诶呀!</strong> 提交时出现错误.
        </div>
    @elseif (session()->exists('success'))
        <div class="alert alert-success" role="alert">
          <strong>很棒!</strong> {!! Session::pull('success') !!}
        </div>
    @elseif (session()->exists('warning'))
        <div class="alert alert-warning" role="alert">
            {!! Session::pull('warning') !!}
        </div>
    @elseif (session()->exists('error'))
        <div class="alert alert-danger" role="alert">
            <strong>诶呀!</strong> {!! Session::pull('error') !!}
        </div>
    @endif
@endif
