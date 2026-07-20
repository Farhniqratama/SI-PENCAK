import os
import re

files_to_fix = [
    'admin/pencairan_draft.blade.php',
    'operator/pencairan_detail.blade.php',
    'admin/mahasiswa_form.blade.php',
    'admin/verifikasi_1.blade.php',
    'admin/prodi_form.blade.php',
    'admin/verifikasi_detail.blade.php',
    'admin/verifikasi_edit.blade.php',
    'operator/userpt_form.blade.php',
    'operator/pt_form.blade.php',
    'operator/index.blade.php',
    'admin/index.blade.php'
]

base_dir = '/Applications/MAMP/htdocs/sipencak-lldikti/resources/views'

for file in files_to_fix:
    path = os.path.join(base_dir, file)
    if not os.path.exists(path):
        continue
        
    with open(path, 'r') as f:
        content = f.read()

    # Replace <div class="card-header bg-white ..."> with <div class="card-header sipencak-blue-header ...">
    # Actually, we can just replace 'bg-white' with 'bg-primary text-white' inside card-header, 
    # but the easiest is to just remove bg-white and let app.blade.php handle the background, 
    # OR we apply explicit styles to make it safe.
    # Let's add a custom class 'sipencak-blue-header' and remove 'bg-white', 'text-primary', 'text-dark'.

    # Let's just do targeted string replacements.
    if 'card-header bg-white' in content:
        # For pencairan_draft
        content = content.replace(
            '<div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">',
            '<div class="card-header bg-primary bg-gradient text-white border-0 p-4 d-flex justify-content-between align-items-center">'
        )
        content = content.replace(
            '<h6 class="fw-bold mb-0 text-primary uppercase">',
            '<h6 class="fw-bold mb-0 text-white uppercase">'
        )
        content = content.replace(
            '<span class="badge bg-white text-primary border px-3 py-2 rounded-pill shadow-sm small fw-bold">',
            '<span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm small fw-bold">'
        )
        
        # For operator/pencairan_detail
        content = content.replace(
            '<div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">',
            '<div class="card-header bg-primary bg-gradient text-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">'
        )
        content = content.replace(
            '<p class="text-muted fs-13 mb-0 mt-1">Reference ID: <span class="fw-bold text-dark">',
            '<p class="text-white-50 fs-13 mb-0 mt-1">Reference ID: <span class="fw-bold text-white">'
        )

        # For admin/mahasiswa_form and admin/prodi_form
        content = content.replace(
            '<div class="card-header bg-white border-bottom p-4">',
            '<div class="card-header bg-primary bg-gradient text-white border-0 p-4">'
        )
        content = content.replace(
            '<h5 class="mb-0 fw-bold text-primary">',
            '<h5 class="mb-0 fw-bold text-white">'
        )

        # For admin/verifikasi_1
        content = content.replace(
            '<div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">',
            '<div class="card-header bg-primary bg-gradient text-white border-0 p-4 d-flex justify-content-between align-items-center">'
        )

        # For admin/verifikasi_detail
        content = content.replace(
            '<h5 class="mb-0 fw-bold text-primary"><i class="ri-history-line me-2"></i> Log Aktivitas Verifikasi</h5>',
            '<h5 class="mb-0 fw-bold text-white"><i class="ri-history-line me-2"></i> Log Aktivitas Verifikasi</h5>'
        )

        # For admin/verifikasi_edit
        content = content.replace(
            '<h6 class="fw-bold mb-0 text-primary uppercase"><i class="ri-edit-2-line me-2"></i>',
            '<h6 class="fw-bold mb-0 text-white uppercase"><i class="ri-edit-2-line me-2"></i>'
        )

        # For index.blade.php
        content = content.replace(
            '<div class="card-header bg-white border-0 pt-4 pb-0 px-4">',
            '<div class="card-header bg-primary bg-gradient text-white border-0 pt-4 pb-0 px-4">'
        )
        content = content.replace(
            '<h5 class="fw-bold text-primary mb-0">',
            '<h5 class="fw-bold text-white mb-0">'
        )
        content = content.replace(
            '<h5 class="fw-bold mb-0 text-primary">',
            '<h5 class="fw-bold text-white mb-0">'
        )

        with open(path, 'w') as f:
            f.write(content)
        print(f"Fixed {file}")

