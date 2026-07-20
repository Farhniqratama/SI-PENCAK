<?php

$dir = __DIR__ . '/app/Http/Controllers';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $original = $content;

        // 1. paginate(X, 'default') -> paginate(X)
        $content = preg_replace('/->paginate\((\d+),\s*\'default\'\)/', '->paginate($1)', $content);
        
        // 2. like('col', $var) -> where('col', 'like', "%" . $var . "%")
        $content = preg_replace('/->like\(\s*\'([^\']+)\',\s*(\$[a-zA-Z0-9_]+)\s*\)/', '->where(\'$1\', \'like\', "%" . $2 . "%")', $content);

        // 3. orLike('col', $var) -> orWhere('col', 'like', "%" . $var . "%")
        $content = preg_replace('/->orLike\(\s*\'([^\']+)\',\s*(\$[a-zA-Z0-9_]+)\s*\)/', '->orWhere(\'$1\', \'like\', "%" . $2 . "%")', $content);

        // 4. groupStart() and groupEnd()
        // We will look for $model->groupStart() ... ->groupEnd()
        // Or $builder->groupStart() ... ->groupEnd()
        // Or $query->groupStart() ... ->groupEnd()
        
        $content = preg_replace_callback('/(\$[a-zA-Z0-9_]+)->groupStart\(\)\s*(.*?)\s*->groupEnd\(\)/s', function($matches) {
            $baseVar = $matches[1];
            $inner = $matches[2];
            
            // Extract the variable used in the inner query (like $search or $keyword)
            preg_match('/\$[a-zA-Z0-9_]+/', $inner, $varMatches);
            $useVar = $varMatches[0] ?? '$search';
            
            // Replace the chained calls inside to use $q instead of chaining off groupStart implicitly
            // Since it's chained, $inner starts with `->where(...)`
            // We just need to prepend `$q` to it.
            $inner = '$q' . $inner;
            
            return $baseVar . "->where(function(\$q) use ($useVar) {\n                " . $inner . ";\n            })";
        }, $content);

        if ($content !== $original) {
            file_put_contents($file->getRealPath(), $content);
            echo "Fixed: " . $file->getFilename() . "\n";
        }
    }
}
