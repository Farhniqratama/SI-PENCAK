<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{!! __('Errors.badRequest') !!}</title>

    
</head>
<body>
<div class="wrap">
    <h1>400</h1>

    <p>
        @if(ENVIRONMENT !== 'production')
            {!! nl2br(e($message)) !!}
        @else
            {!! __('Errors.sorryBadRequest') !!}
        @endif
    </p>
</div>
</body>
</html>
