import json

with open('package.json', 'r') as f:
    root_pkg = json.load(f)

with open('attex_template/Admin/package.json', 'r') as f:
    attex_pkg = json.load(f)

# Merge dependencies
if 'dependencies' not in root_pkg:
    root_pkg['dependencies'] = {}
if 'dependencies' in attex_pkg:
    for k, v in attex_pkg['dependencies'].items():
        root_pkg['dependencies'][k] = v

# Merge devDependencies (keeping root's newer versions for vite etc)
if 'devDependencies' not in root_pkg:
    root_pkg['devDependencies'] = {}
if 'devDependencies' in attex_pkg:
    for k, v in attex_pkg['devDependencies'].items():
        if k not in root_pkg['devDependencies']:
            root_pkg['devDependencies'][k] = v

with open('package.json', 'w') as f:
    json.dump(root_pkg, f, indent=4)
