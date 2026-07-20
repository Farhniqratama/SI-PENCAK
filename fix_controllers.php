<?php

function fixControllers($path) {
    $files = glob($path . '*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $originalContent = $content;

        // Fix missing use statement for Controller if extending Controller
        if (strpos($content, 'extends Controller') !== false && strpos($content, 'use App\Http\Controllers\Controller;') === false) {
            $content = preg_replace('/namespace App\\\\Http\\\\Controllers\\\\(Operator|Admin);/', "namespace App\\Http\\Controllers\\$1;\n\nuse App\\Http\\Controllers\\Controller;", $content);
        }

        // Remove helper(...) calls
        $content = preg_replace('/helper\([\'"].*?[\'"]\);/', '', $content);

        // Fix Model Calls: 
        // CI4: $model->findAll() -> Eloquent: $model->get() or $model::all()
        $content = preg_replace('/->findAll\(\)/', '->get()', $content);
        
        // CI4: $model->find($id) -> Eloquent: $model->find($id) (This is compatible)

        // CI4: $model->save($data) or insert($data) -> Eloquent $model->create($data)
        // Note: Eloquent models need guarded/fillable for create. Our generated models have fillable.
        // Wait, CI4's insert($data)
        $content = preg_replace('/->insert\((.*?)\)/', '->create($1)', $content);
        
        // CI4: $model->update($id, $data) -> Eloquent $model->where('id', $id)->update($data)
        // We can do a simpler replace for ->update($id, $data) if they are 2 arguments.
        $content = preg_replace('/->update\(\s*(\$id|\$[a-zA-Z0-9_]+)\s*,\s*(\$.*?)\s*\)/', '->where(\'id\', $1)->update($2)', $content);
        
        // CI4: $model->delete($id) -> Eloquent: $model->where('id', $id)->delete() or destroy($id)
        $content = preg_replace('/->delete\(\s*(\$id|\$[a-zA-Z0-9_]+)\s*\)/', '->where(\'id\', $1)->delete()', $content);

        // CI4 session()->set(...) -> Laravel session()->put(...)
        $content = preg_replace('/session\(\)->set\(/', 'session()->put(', $content);
        
        // CI4 session()->get(...) -> Laravel session(...)
        $content = preg_replace('/session\(\)->get\(/', 'session(', $content);
        
        // request()->getPost(...) was already replaced to request(...)
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Fixed " . basename($file) . "\n";
        }
    }
}

fixControllers(__DIR__ . '/app/Http/Controllers/Operator/');
fixControllers(__DIR__ . '/app/Http/Controllers/Admin/');
