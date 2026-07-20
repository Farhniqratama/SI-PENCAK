<?php

$map = [
    'resources/views/operator/laporan_home.blade.php' => 'histori',
    'resources/views/operator/pt_list.blade.php' => 'data',
    'resources/views/operator/pencairan_list.blade.php' => 'pencairans',
    'resources/views/operator/pencairan_detail.blade.php' => 'dataMahasiswa',
    'resources/views/operator/informasi_list.blade.php' => 'data',
    'resources/views/operator/userpt_list.blade.php' => 'data',
    'resources/views/operator/laporan/index.blade.php' => 'pencairans',
    'resources/views/admin/pencairan_draft.blade.php' => 'pts',
    'resources/views/admin/prodi_list.blade.php' => 'data',
    'resources/views/admin/verifikasi_list.blade.php' => 'pts',
    'resources/views/admin/verifikasi_2.blade.php' => 'mahasiswa',
    'resources/views/admin/verifikasi_detail.blade.php' => 'dataMahasiswa',
    'resources/views/admin/informasi_list.blade.php' => 'data',
    'resources/views/admin/verifikasi_3.blade.php' => 'mahasiswa',
    'resources/views/admin/mahasiswa_list.blade.php' => 'data',
    'resources/views/admin/laporan/home.blade.php' => 'histori',
    'resources/views/admin/laporan/list_by_pt.blade.php' => 'pencairans',
];

$dir = __DIR__ . '/';
foreach ($map as $file => $var) {
    $path = $dir . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        // We will replace {!! $pager->links(...) !!} with {{ $var->links('pagination::bootstrap-5') }}
        $content = preg_replace('/\{\!\!\s*\$pager->links\([^)]*\)\s*\!\!\}/', '{{ $' . $var . '->links(\'pagination::bootstrap-5\') }}', $content);
        file_put_contents($path, $content);
        echo "Fixed $file using $var\n";
    } else {
        echo "Missing $file\n";
    }
}
