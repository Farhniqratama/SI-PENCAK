import os
import re
import glob

files = glob.glob('resources/views/**/*.blade.php', recursive=True)

for file in files:
    with open(file, 'r') as f:
        content = f.read()
    
    original = content
    # Replace table styles
    content = re.sub(r'<table class="table table-borderless table-hover align-middle mb-0">', '<table class="table table-striped table-centered mb-0">', content)
    content = re.sub(r'<table class="table table-hover table-borderless align-middle mb-0">', '<table class="table table-striped table-centered mb-0">', content)
    
    # Replace thead styles
    content = re.sub(r'<thead class="table-light">', '<thead>', content)
    
    # Replace card styles
    content = re.sub(r'class="card\s+border-0\s+shadow-sm\s+rounded-4([^"]*)"', r'class="card\1"', content)
    
    # Button rounded-pill
    # content = re.sub(r'btn-primary rounded-pill', 'btn-primary', content) # Optional, rounded-pill is standard bootstrap 5

    if content != original:
        with open(file, 'w') as f:
            f.write(content)
        print(f"Updated {file}")

