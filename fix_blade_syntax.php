<?php

$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.php') !== false) {
        $content = file_get_contents($file->getRealPath());
        $original = $content;

        // Replace {!! $this->extend('...') !!} with @extends('...')
        // Also replace $this->extend("...")
        $content = preg_replace('/\{\!\!\s*\$this->extend\([\'"]([^\'"]+)[\'"]\)\s*\!\!\}/', '@extends(\'$1\')', $content);
        
        // Convert layouts/app to layouts.app just in case
        $content = str_replace("@extends('layouts/app')", "@extends('layouts.app')", $content);

        // Replace {!! $this->section('...') !!} with @section('...')
        $content = preg_replace('/\{\!\!\s*\$this->section\([\'"]([^\'"]+)[\'"]\)\s*\!\!\}/', '@section(\'$1\')', $content);

        // Replace {!! $this->endSection() !!} with @endsection
        $content = preg_replace('/\{\!\!\s*\$this->endSection\(\)\s*\!\!\}/', '@endsection', $content);

        // Replace {!! $this->include('...') !!} with @include('...')
        $content = preg_replace('/\{\!\!\s*\$this->include\([\'"]([^\'"]+)[\'"]\)\s*\!\!\}/', '@include(\'$1\')', $content);

        if ($content !== $original) {
            file_put_contents($file->getRealPath(), $content);
            echo "Fixed Blade syntax in: " . $file->getFilename() . "\n";
        }
    }
}
