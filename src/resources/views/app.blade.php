<!DOCTYPE html>
<html>
<head id="head">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="icon" href="/css/favicon.ico">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <script src="{{ mix('/js/app.js') }}" defer></script>
    @routes
    @translations

</head>
<body class="hold-transition sidebar-mini">

@inertia

<div id="destination"></div>

</body>
</html>
