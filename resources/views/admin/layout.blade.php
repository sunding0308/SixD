<html lang="en">
<head>
    @include('admin.includes.head')
    <link rel="icon" href="/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <link href="/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">

    @stack('head-scripts')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.params = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>
    </script>
</head>
<body class="superadmin">

<div id="wrapper">

    <main class="main" role="main">

        @include('admin.includes.sidebar')

        <div class="main-content">
            @include('admin.includes.header')
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3 text-center">
                        @include('admin.includes.message')
                    </div>
                </div>
                @yield('content')
            </div>
        </div><!-- .main-content -->

    </main><!-- .main -->

</div><!-- .wrapper -->

@include('admin.includes.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="/js/bootstrap.min.js"></script>
@stack('foot-scripts')
</body>
</html>

@stack('js')