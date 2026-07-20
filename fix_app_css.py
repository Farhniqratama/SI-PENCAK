import os

path = '/Applications/MAMP/htdocs/sipencak-lldikti/resources/views/layouts/app.blade.php'

with open(path, 'r') as f:
    content = f.read()

# Append some CSS to fix text colors inside card-header
if '.card-header .text-muted' not in content:
    css_rules = """
        .card-header .text-muted {
            color: rgba(255, 255, 255, 0.75) !important;
        }
        .card-header .text-primary {
            color: rgba(255, 255, 255, 0.95) !important;
        }
        .card-header .text-dark {
            color: #ffffff !important;
        }
"""
    # Insert it right before </style> inside the head, or append to the first <style>
    content = content.replace('</style>', css_rules + '</style>', 1)
    
    with open(path, 'w') as f:
        f.write(content)
    print("Added CSS overrides to app.blade.php")
