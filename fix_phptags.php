<?php

$dir = new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS);
$it  = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

foreach ($it as $fileinfo) {
    if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
        $filePath = $fileinfo->getRealPath();
        $content = file_get_contents($filePath);
        $origContent = $content;

        // Fix php endif
        $content = preg_replace('/<\x3fphp\s+endif\s*;?\s*\x3f\x3e/s', '@endif', $content);
        
        // Fix php endfor
        $content = preg_replace('/<\x3fphp\s+endfor\s*;?\s*\x3f\x3e/s', '@endfor', $content);

        // Fix php if(...) :
        $content = preg_replace('/<\x3fphp\s+if\s*\((.*?)\)\s*:\s*\x3f\x3e/s', '@if($1)', $content);
        
        // Fix php for(...) :
        $content = preg_replace('/<\x3fphp\s+for\s*\((.*?)\)\s*:\s*\x3f\x3e/s', '@for($1)', $content);

        // Change generic php to @php
        $content = preg_replace('/<\x3fphp(.*?)\x3f\x3e/s', '@php$1@endphp', $content);

        if ($content !== $origContent) {
            file_put_contents($filePath, $content);
            echo "Fixed php tags in " . basename($filePath) . "\n";
        }
    }
}
