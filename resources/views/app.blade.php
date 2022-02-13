<!DOCTYPE html>
<html>
<head id="head">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="icon" href="/img/seat_plus_logo.svg">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    @routes
    @translations

</head>
<body class="hold-transition sidebar-mini">

@inertia

<div id="destination"></div>

<script src="{{ mix('/js/manifest.js') }}" defer></script>
<script src="{{ mix('/js/vendor.js') }}" defer></script>
<script src="{{ mix('/js/app.js') }}" defer></script>
</body>
</html>
