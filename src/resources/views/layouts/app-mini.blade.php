
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>SeAT plus| @yield('title', 'Eve Online API Tool')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body class="hold-transition login-page">

<div class="login-box">
  @include('web::includes.flash-message')
  <div class="login-logo">
    <a href="../../index2.html"><b>Admin</b>LTE</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">

    <div class="card-body login-card-body">
      @yield('content')
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<script src="{{ mix('/js/app.js') }}"></script>

</body>
</html>