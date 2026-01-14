<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .powered-by {
            margin-top: 10px;
            font-size: 12px;
            color: #999;
        }

        .powered-by a {
            color: #666;
            text-decoration: none;
        }

        .powered-by a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class='body'>
    <div>
        {{ $slot }}


        <div class="powered-by">
            {{ __('chatter::views/mail/follower-mail.powered-by') }} <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
        </div>
    </div>
</body>

</html>
