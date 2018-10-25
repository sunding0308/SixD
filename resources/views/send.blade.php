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

                    {{ Form::open(['url' => route('post_send'), 'method' => 'POST', 'name'=>'form_version_creation', 'class' => 'form-horizontal', 'files' => true]) }}

                    <div class="ibox">
                        <div class="row">
                            <div class="col-md-6 offset-md-3 text-center">
                                @include('admin.includes.message')
                            </div>
                        </div>

                        <div class="ibox-content">

                                <div class="form-group row{{ $errors->has('device') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">设备ID*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="device" value="{{ old('device') }}" class="form-control">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'device'])
                                    </div>
                                </div>
                                <div class="form-group row{{ $errors->has('data') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">数据*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="data" value="{{ old('data') }}" class="form-control" placeholder="例：00 90 4C C5 12 38">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'data'])
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
