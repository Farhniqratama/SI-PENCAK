<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{!! __('Errors.pageNotFound') !!}</title>

    
</head>
<body>
    <div class="wrap">
        <h1>404</h1>

        <p>
            @if(ENVIRONMENT !== 'production')
                {!! nl2br(e($message)) !!}
            @else
                {!! __('Errors.sorryCannotFind') !!}
            @endif
        </p>
    </div>
</body>
</html>
