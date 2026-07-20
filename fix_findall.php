<?php

function fixFindAllArgs($path) {
    $files = glob($path . '*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;

        // Fix findAll(X) to take(X)->get()
        $content = preg_replace('/->findAll\((\d+)\)/', '->take($1)->get()', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Fixed findAll args in " . basename($file) . "\n";
        }
    }
}

fixFindAllArgs(__DIR__ . '/app/Http/Controllers/Operator/');
fixFindAllArgs(__DIR__ . '/app/Http/Controllers/Admin/');
