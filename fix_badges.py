import os
import re

files_to_fix = [
    'operator/pencairan_list.blade.php',
    'admin/verifikasi_list.blade.php',
    'admin/mahasiswa_show.blade.php',
    'admin/verifikasi_detail.blade.php'
]

base_dir = '/Applications/MAMP/htdocs/sipencak-lldikti/resources/views'

for file in files_to_fix:
    path = os.path.join(base_dir, file)
    if not os.path.exists(path):
        continue
        
    with open(path, 'r') as f:
        content = f.read()

    # Replace specific light badges
    content = content.replace(
        '<span class="badge bg-light text-dark border">',
        '<span class="badge bg-primary bg-gradient text-white border-0 shadow-sm px-2 py-1">'
    )
    
    # admin/verifikasi_list.blade.php doesn't use a badge for semester! It uses:
    # <span class="text-dark fw-bold">{{ $semester }}</span>
    # <span class="text-muted">/ {!! $year !!}</span>
    if file == 'admin/verifikasi_list.blade.php':
        content = content.replace(
            '<span class="text-dark fw-bold">{{ $semester }}</span>',
            '<span class="badge bg-primary bg-gradient text-white border-0 shadow-sm px-2 py-1">{{ $semester }}</span>'
        )
        content = content.replace(
            '<span class="text-muted">/ {!! $year !!}</span>',
            '<span class="badge bg-primary-subtle text-primary border-0 shadow-sm px-2 py-1">/ {!! $year !!}</span>'
        )

    # pencairan_draft doesn't use a badge either
    # Wait, let's fix pencairan_draft semester too
    with open(path, 'w') as f:
        f.write(content)
    print(f"Fixed {file}")

# Do admin/pencairan_draft.blade.php
path_draft = os.path.join(base_dir, 'admin/pencairan_draft.blade.php')
with open(path_draft, 'r') as f:
    draft_content = f.read()
draft_content = draft_content.replace(
    '<span class="text-dark fw-bold">{{ $semester }}</span>',
    '<span class="badge bg-primary bg-gradient text-white border-0 shadow-sm px-2 py-1">{{ $semester }}</span>'
)
draft_content = draft_content.replace(
    '<span class="text-muted">/ {!! $year !!}</span>',
    '<span class="badge bg-primary-subtle text-primary border-0 shadow-sm px-2 py-1">/ {!! $year !!}</span>'
)
with open(path_draft, 'w') as f:
    f.write(draft_content)
print("Fixed admin/pencairan_draft.blade.php")

