<html lang="en">
<head>
    <link rel="icon" href="/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <link href="/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.params = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>
    </script>
</head>
<body class="superadmin">

<div id="wrapper">

    <main class="main" role="main">

        <div class="main-content">
            <div class="container">
                <div class="row justify-content-center">

                <div class="col-md-9" style="padding-right: 15px;right: 80px;top: 200px;">

                    {{ Form::open(['url' => '', 'method' => 'POST', 'name'=>'form_version_creation', 'class' => 'form-horizontal', 'files' => true]) }}
                    <div class="ibox">
                        <div class="row">
                            <div class="col-md-6 offset-md-3 text-center">
                                @include('admin.includes.message')
                            </div>
                        </div>

                        <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">接口编号*</label>
                                    <div class="col-md-9">
                                        <select id="apiNo" class="form-control" name="api_no" required>
                                            <option value="" disabled selected>选择接口编号</option>
                                            @foreach(config('api_no') as $key => $value)
                                                <option value="{{ $key }}" myUrl="{{ $value }}">{{ $key }}</option>
                                            @endforeach
                                        </select>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'api_no'])
                                    </div>
                                </div>

                                <div class="form-group row{{ $errors->has('device') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">machineId*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="device" value="{{ old('device') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'device'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('position') ? " has-danger" : "" }} WS013 WS006 WS010 WS019 hidden">
                                    <label class="col-md-3 control-label">positionNumber*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="position" value="{{ old('position') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'position'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('service_content') ? " has-danger" : "" }} WS013 hidden">
                                    <label class="col-md-3 control-label">serviceContent*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="service_content" value="{{ old('service_content') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'service_content'])
                                    </div>
                                </div>

                                <div class="form-group row{{ $errors->has('complete_status') ? " has-danger" : "" }} WS017 hidden">
                                    <label class="col-md-3 control-label">completeStatus*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="complete_status" value="2" class="form-control" readonly="readonly">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'complete_status'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('position_up') ? " has-danger" : "" }} WS017 hidden">
                                    <label class="col-md-3 control-label">positionUp*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="position_up" value="{{ old('position_up') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'position_up'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('serial_up') ? " has-danger" : "" }} WS017 hidden">
                                    <label class="col-md-3 control-label">serialNumberUp*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="serial_up" value="{{ old('serial_up') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'serial_up'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('position_down') ? " has-danger" : "" }} WS017 hidden">
                                    <label class="col-md-3 control-label">positionDown*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="position_down" value="{{ old('position_down') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'position_down'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('serial_down') ? " has-danger" : "" }} WS017 hidden">
                                    <label class="col-md-3 control-label">serialNumberDown*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="serial_down" value="{{ old('serial_down') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'serial_down'])
                                    </div>
                                </div>

                                <div class="form-group row{{ $errors->has('serial') ? " has-danger" : "" }} WS010 WS019 hidden">
                                    <label class="col-md-3 control-label">serialNumber*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="serial" value="{{ old('serial') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'serial'])
                                    </div>
                                </div>

                                <div class="form-group row{{ $errors->has('container_id') ? " has-danger" : "" }} WS019 hidden">
                                    <label class="col-md-3 control-label">machineContainerId*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="container_id" value="{{ old('container_id') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'container_id'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('container_position') ? " has-danger" : "" }} WS019 hidden">
                                    <label class="col-md-3 control-label">containerNumber*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="container_position" value="{{ old('container_position') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'container_position'])
                                    </div>
                                </div>

                        </div><!-- .ibox-content -->

                    </div><!-- .ibox -->

                    <div class="form-save">
                        {{ Form::submit('发送', ['class' => 'btn btn-primary']) }}
                    </div>

                    {{ Form::close() }}

                </div><!-- .col* -->

                </div><!-- .row -->
            </div>
        </div><!-- .main-content -->

    </main><!-- .main -->

</div><!-- .wrapper -->

@include('admin.includes.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="/js/bootstrap.min.js"></script>
</body>
</html>

<script>
    var previous;
    $('#apiNo').change(function(){ 
        // console.log($(this).val());
        // console.log(previous);
        // console.log($('option:selected', this).attr('myUrl'));
        $('.'+previous).addClass('hidden');
        $('.'+$(this).val()).removeClass('hidden');
        $("[name='form_version_creation']").attr('action', $('option:selected', this).attr('myUrl'));
        previous = $(this).val();
    });
</script>
