<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>KW-API</title>

    <link rel="stylesheet" href="vendor/ng-admin/ng-admin.min.css">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
</head>
<body id="app-layout" ng-app="myApp">
    <div ui-view></div>
    <!-- JavaScripts -->
    <script type="text/javascript">
        var apiKey = window.localStorage.getItem("kw_api_key_store");
        if (!apiKey) {
            window.location.href = "./login.html";
        }
        function logout() {
            window.localStorage.removeItem("kw_api_key_store");
            window.location.href = "./login.html";
        }
    </script>
    <script src="vendor/ng-admin/ng-admin.min.js" type="text/javascript"></script>
    <script src="admin.bundle.js" type="text/javascript"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
