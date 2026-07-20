<?php

$dir = new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS);
$it  = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

foreach ($it as $fileinfo) {
    if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
        $filePath = $fileinfo->getRealPath();
        $content = file_get_contents($filePath);
        $origContent = $content;

        // Fix php endforeach; else : 
        $content = preg_replace('/<\?php\s+endforeach;\s*else\s*:\s*\?'.'>/s', '@endforeach' . "\n" . '@else', $content);
        
        // Fix php endforeach; endif; 
        $content = preg_replace('/<\?php\s+endforeach;\s*endif;\s*\?'.'>/s', '@endforeach' . "\n" . '@endif', $content);

        // Fix php endforeach 
        $content = preg_replace('/<\?php\s+endforeach\s*;?\s*\?'.'>/s', '@endforeach', $content);

        // Fix @if(...) : foreach (...)
        $content = preg_replace('/(@if\([^)]+\))\s*:\s*foreach\s*\((.*?)\)/s', '$1' . "\n" . '@foreach($2)', $content);

        if ($content !== $origContent) {
            file_put_contents($filePath, $content);
            echo "Fixed foreach loops in " . basename($filePath) . "\n";
        }
    }
}
