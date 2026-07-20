@php
    $exception = $exception ?? null;
    $title = $title ?? ($exception ? class_basename($exception) : 'Application Error');
    $message = $exception ? $exception->getMessage() : 'Terjadi kesalahan pada aplikasi.';
    $file = $exception ? $exception->getFile() : null;
    $line = $exception ? $exception->getLine() : null;
    $trace = $exception ? $exception->getTrace() : [];
    $code = $exception && $exception->getCode() ? ' #' . $exception->getCode() : '';

    $sourceLines = [];
    if ($file && is_file($file) && $line) {
        $allLines = file($file);
        $start = max($line - 8, 1);
        $end = min($line + 8, count($allLines));

        for ($i = $start; $i <= $end; $i++) {
            $sourceLines[$i] = rtrim($allLines[$i - 1] ?? '');
        }
    }
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}{{ $code }}</title>
    
</head>
<body>
    <main class="wrap">
        <section class="panel">
            <div class="head">
                <h1>{{ $title }}{{ $code }}</h1>
                <div class="muted">{{ now()->format('d M Y H:i:s') }} | PHP {{ PHP_VERSION }} | {{ app()->environment() }}</div>
            </div>
            <div class="body">
                <p class="message">{{ $message }}</p>

                @if($file)
                    <div class="meta">
                        <div><strong>File:</strong> <code>{{ $file }}</code></div>
                        <div><strong>Line:</strong> <code>{{ $line }}</code></div>
                    </div>
                @endif
            </div>
        </section>

        @if(!empty($sourceLines))
            <section class="panel">
                <div class="body">
                    <h2 style="margin-top:0;font-size:16px;">Source</h2>
                </div>
                <pre>@foreach($sourceLines as $number => $source)<span class="{{ $number === $line ? 'line-hit' : '' }}">{{ str_pad($number, 5, ' ', STR_PAD_LEFT) }} | {{ $source }}</span>
@endforeach</pre>
            </section>
        @endif

        @if(!empty($trace))
            <section class="panel">
                <div class="body">
                    <h2 style="margin-top:0;font-size:16px;">Backtrace</h2>
                    <ol class="trace">
                        @foreach($trace as $row)
                            <li>
                                <code>{{ $row['file'] ?? '[internal]' }}{{ isset($row['line']) ? ':' . $row['line'] : '' }}</code>
                                <br>
                                <span class="muted">{{ ($row['class'] ?? '') . ($row['type'] ?? '') . ($row['function'] ?? '') }}()</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>
        @endif
    </main>
</body>
</html>
