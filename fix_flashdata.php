<?php
$dir = new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS);
$it  = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

foreach ($it as $fileinfo) {
    if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') { // .blade.php
        $filePath = $fileinfo->getRealPath();
        $content = file_get_contents($filePath);
        
        $newContent = preg_replace('/session\(\)->getFlashdata\((.*?)\)/', 'session($1)', $content);
        $newContent = preg_replace('/session\(\)->get\((.*?)\)/', 'session($1)', $newContent); // just in case
        
        // Also handle flashdata checking like session()->hasFlashdata() or something if any? No, CI4 uses getFlashdata
        if ($newContent !== $content) {
            file_put_contents($filePath, $newContent);
            echo "Fixed Flashdata in " . basename($filePath) . "\n";
        }
    }
}
