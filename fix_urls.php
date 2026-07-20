<?php
$dir = new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS);
$it  = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

foreach ($it as $fileinfo) {
    if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
        $filePath = $fileinfo->getRealPath();
        $content = file_get_contents($filePath);
        
        $newContent = str_replace('url()', "url('/')", $content);
        $newContent = str_replace('current_url()', "url()->current()", $newContent);
        
        if ($newContent !== $content) {
            file_put_contents($filePath, $newContent);
            echo "Fixed URLs in " . basename($filePath) . "\n";
        }
    }
}
