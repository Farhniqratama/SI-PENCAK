<?php

$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.php') !== false) {
        $content = file_get_contents($file->getRealPath());
        $original = $content;

        // Replace esc( with e(
        $content = preg_replace('/\besc\(/', 'e(', $content);

        // Replace lang( with __(
        $content = preg_replace('/\blang\(/', '__(', $content);

        if ($content !== $original) {
            file_put_contents($file->getRealPath(), $content);
            echo "Fixed helpers in: " . $file->getFilename() . "\n";
        }
    }
}
