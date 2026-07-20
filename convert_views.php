<?php

$dir = new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS);
$it  = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

foreach ($it as $fileinfo) {
    if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
        $filePath = $fileinfo->getRealPath();
        
        $content = file_get_contents($filePath);
        
        // Extends & Sections
        $content = preg_replace('/<\?=\s*\$this->extend\([\'"](.*?)[\'"]\);\s*\?>/', '@extends(\'$1\')', $content);
        $content = preg_replace('/<\?=\s*\$this->section\([\'"](.*?)[\'"]\);\s*\?>/', '@section(\'$1\')', $content);
        $content = preg_replace('/<\?=\s*\$this->endSection\(\);\s*\?>/', '@endsection', $content);
        
        // Includes
        $content = preg_replace('/<\?=\s*\$this->include\([\'"](.*?)[\'"]\);\s*\?>/', '@include(\'$1\')', $content);
        
        // Replace / with . in extends and include
        $content = preg_replace_callback('/@(extends|include)\([\'"](.*?)[\'"]\)/', function($matches) {
            return '@' . $matches[1] . '(\'' . str_replace('/', '.', $matches[2]) . '\')';
        }, $content);

        // Control Structures
        $content = preg_replace('/<\?php\s+if\s*\((.*?)\)\s*:\s*\?>/', '@if($1)', $content);
        $content = preg_replace('/<\?php\s+elseif\s*\((.*?)\)\s*:\s*\?>/', '@elseif($1)', $content);
        $content = preg_replace('/<\?php\s+else\s*:\s*\?>/', '@else', $content);
        $content = preg_replace('/<\?php\s+endif;\s*\?>/', '@endif', $content);
        
        $content = preg_replace('/<\?php\s+foreach\s*\((.*?)\)\s*:\s*\?>/', '@foreach($1)', $content);
        $content = preg_replace('/<\?php\s+endforeach;\s*\?>/', '@endforeach', $content);

        // Echoing
        // Try to handle esc() first
        $content = preg_replace('/<\?=\s*esc\((.*?)\)\s*\?>/', '{{ $1 }}', $content);
        // Then raw echos
        $content = preg_replace('/<\?=\s*(.*?)\s*\?>/', '{!! $1 !!}', $content);

        // URLs
        $content = preg_replace('/base_url\(/', 'url(', $content);
        $content = preg_replace('/site_url\(/', 'url(', $content);
        
        // Forms (csrf)
        $content = preg_replace('/csrf_field\(\)/', 'csrf_field()', $content); // Keep it or replace with @csrf
        $content = str_replace('{!! csrf_field() !!}', '@csrf', $content);

        // Output back to blade file
        $newPath = preg_replace('/\.php$/', '.blade.php', $filePath);
        file_put_contents($newPath, $content);
        unlink($filePath);
        
        echo "Converted " . basename($filePath) . " to Blade\n";
    }
}
