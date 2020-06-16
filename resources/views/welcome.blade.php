<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'C11K') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        th, td {
            padding-left: 10px;
            padding-right: 10px;
        }

        .half-height {
            height: 49vh;
        }

        .third-height {
            height: 32vh;
        }

        .quarter-height {
            height: 24vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
            margin-left: 2%;
            margin-right: 2%;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref half-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">
            Inventory System
        </div>
    </div>
</div>
<div class="flex-center position-ref quarter-height">
    <div>
        Hit the login button in the upper right hand corner and login as one of the demo users.
        Note that this demo system will not send nor receive emails. This demo system also resets
        the database every 2 hours. If some knucklehead messed up the database or changed the
        passwords and you can't login, please wait 2 hours and try again. If the issue persists,
        please&nbsp;<a href="https://github.com/jefhar/inventory/issues/new">start a new issue</a>
        at github, and I'll fix it as soon as I am able.
    </div>

</div>
<div class="flex-center position-ref">
    <table>
        <thead>
        <tr>
            <th>User Name</th>
            <th>User E-mail</th>
            <th>User Password</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Default SuperAdmin</td>
            <td>superadmin@example.com</td>
            <td>password</td>
        </tr>
        <tr>
            <td>Default Owner</td>
            <td>owner@example.com</td>
            <td>password</td>
        </tr>
        <tr>
            <td>Default Sales Rep</td>
            <td>salesrep@example.com</td>
            <td>password</td>
        </tr>
        <tr>
            <td>Default Technician</td>
            <td>technician@example.com</td>
            <td>password</td>
        </tr>
        <tr>
            <td>Default Employee</td>
            <td>employee@example.com</td>
            <td>password</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
