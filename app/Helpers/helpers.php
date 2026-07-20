<?php

if (!function_exists('get_attex_asset')) {
    function get_attex_asset($path, $manifest = null)
    {
        if ($manifest === null) {
            $manifestPath = public_path('assets/attex/build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
            }
        }
        
        if ($manifest && isset($manifest[$path])) {
            return asset('assets/attex/build/' . $manifest[$path]['file']);
        }
        
        return asset('assets/attex/build/' . $path);
    }
}

if (!function_exists('word_limiter')) {
    function word_limiter($str, $limit = 100, $endChar = '&#8230;')
    {
        return \Illuminate\Support\Str::words(strip_tags((string) $str), $limit, html_entity_decode($endChar));
    }
}

if (!function_exists('ci_upload_name')) {
    function ci_upload_name($file)
    {
        return $file ? $file->hashName() : null;
    }
}

if (!function_exists('tanggal_indonesia')) {
    function tanggal_indonesia($tanggal)
    {
        if (empty($tanggal)) {
            return '-';
        }

        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $timestamp = strtotime($tanggal);

        if ($timestamp === false) {
            return $tanggal;
        }

        return date('j', $timestamp) . ' ' . $bulan[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }
}

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return url($path);
    }
}

if (!function_exists('current_url')) {
    function current_url($path = '')
    {
        return $path && $path !== '/' ? url($path) : url()->current();
    }
}
