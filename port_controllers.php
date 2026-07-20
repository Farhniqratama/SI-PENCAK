<?php
$ci4Controllers = '/Applications/MAMP/htdocs/sipencak-lldikti/app/Controllers/';
$laravelControllers = '/Applications/MAMP/htdocs/laravel_temp/app/Http/Controllers/';

function portDirectory($src, $dest, $namespace) {
    if (!is_dir($dest)) mkdir($dest, 0777, true);
    $files = glob($src . '*.php');
    foreach ($files as $file) {
        $basename = basename($file);
        if ($basename == 'BaseController.php' || $basename == 'Auth.php') continue;
        
        $content = file_get_contents($file);
        
        // Namespaces & Base Classes
        $content = str_replace("namespace App\Controllers\\$namespace;", "namespace App\Http\Controllers\\$namespace;", $content);
        $content = str_replace("namespace App\Controllers;", "namespace App\Http\Controllers;", $content);
        $content = str_replace("use App\Controllers\BaseController;", "use App\Http\Controllers\Controller;\nuse Illuminate\Http\Request;", $content);
        $content = str_replace("extends BaseController", "extends Controller", $content);
        $content = str_replace("extends BaseOperatorController", "extends Controller", $content);
        
        // Models
        $content = preg_replace('/use App\\\\Models\\\\(\w+)Model;/', 'use App\Models\\\$1;', $content);
        $content = preg_replace('/new (\w+)Model\(\)/', 'new \\App\\Models\\\$1()', $content);
        
        // Request & Session
        $content = preg_replace('/\$this->request->getPost\((.*?)\)/', 'request($1)', $content);
        $content = preg_replace('/\$this->request->getGet\((.*?)\)/', 'request($1)', $content);
        $content = preg_replace('/\$this->request->getFile\((.*?)\)/', 'request()->file($1)', $content);
        $content = str_replace("session()->setFlashdata(", "session()->flash(", $content);
        
        // Redirects
        $content = preg_replace('/return redirect\(\)->to\((.*?)\)/', 'return redirect($1)', $content);
        $content = preg_replace('/return redirect\(\)->back\(\)/', 'return back()', $content);
        
        // Views
        // view('operator/pt_list', $data) => view('operator.pt_list', $data)
        $content = preg_replace('/view\([\'"](.*?)\/(.*?)[\'"]/', 'view(\'$1.$2\'', $content);

        // Model saves
        // CI4 uses $model->save($data) or $model->insert($data). Laravel Eloquent uses $model->create() or $model->update()
        // We'll leave it as is and use Eloquent logic manually or let it break and fix it. Actually Laravel has ->save() if instance exists, but for arrays we use ::create() or ::insert().
        // To be safe, let's leave it, but it might need manual adjustments.

        file_put_contents($dest . $basename, $content);
        echo "Ported $basename to $dest\n";
    }
}

portDirectory($ci4Controllers . 'Operator/', $laravelControllers . 'Operator/', 'Operator');
portDirectory($ci4Controllers . 'Admin/', $laravelControllers . 'Admin/', 'Admin');
