import os
import re
import glob

files = glob.glob('resources/scss/custom/icons/*.scss')

for file in files:
    with open(file, 'r') as f:
        content = f.read()
    
    # Replace url('../fonts/ with url('../../../fonts/
    content = content.replace("url('../fonts/", "url('../../../fonts/")
    content = content.replace('url("../fonts/', 'url("../../../fonts/')
    
    with open(file, 'w') as f:
        f.write(content)
    print(f"Updated {file}")

