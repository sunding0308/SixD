<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/images/favicon.ico">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
	<link href="/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
    <title>@stack('title')</title>
    @stack('script-head')
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- <link href="/css/style.css?v=2" rel="stylesheet"> 
    <link href="/css/custom.css?v=2" rel="stylesheet"> -->
    <link href="/css/style.css" rel="stylesheet"> 
    <link href="/css/custom.css" rel="stylesheet"> 

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.params = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>
    </script>
    

    @stack('header_scripts')

  </head>

  <body @stack('body_attr')>

@yield('body')

	<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- Moved up for HS Editor -->
@if (!isset($jquery_in_head))
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
@endif
    
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.6.1/clipboard.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
    <script src="/js/scripts.js?v=3.1"></script>

    @stack('js')
  </body>
</html>
