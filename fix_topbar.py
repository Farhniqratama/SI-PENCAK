import re

with open('resources/views/layouts/topbar.blade.php', 'r') as f:
    content = f.read()

# Replace auth()->user()->name
content = content.replace('{{ auth()->user()->name }}', "{{ $displayName ?? 'User' }}")
# Replace route('second', ...)
content = re.sub(r"\{\{ route\('second', [^}]+\} \}\}", "url('#')", content)
content = re.sub(r"\{\{\s*route\('second',\s*\[[^\]]+\]\)\s*\}\}", "url('#')", content)
# Replace logout form with simple link
logout_pattern = re.compile(r'<form method="POST" action="\{\{ route\(\'logout\'\) \}\}">(.*?)</form>', re.DOTALL)
logout_replacement = r'<a href="{{ url(\'logout\') }}" class="dropdown-item"><i class="ri-logout-box-line fs-18 align-middle me-1"></i><span>Logout</span></a>'
content = logout_pattern.sub(logout_replacement, content)

# Fix image paths
content = re.sub(r'(src|href)="/images/', r'\1="{{ url(\'assets/attex/images/', content)
content = content.replace('.jpg"', '.jpg\') }}"')
content = content.replace('.png"', '.png\') }}"')

with open('resources/views/layouts/topbar.blade.php', 'w') as f:
    f.write(content)

