<?php
$modelsPath = '/Applications/MAMP/htdocs/sipencak-lldikti/app/Models/';
$laravelModelsPath = '/Applications/MAMP/htdocs/laravel_temp/app/Models/';

$files = glob($modelsPath . '*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    preg_match('/class\s+(\w+)Model/', $content, $classMatch);
    if (!$classMatch) continue;
    $className = $classMatch[1];
    
    preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $tableMatch);
    $table = $tableMatch ? $tableMatch[1] : strtolower($className) . 's';
    
    preg_match('/protected\s+\$primaryKey\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $pkMatch);
    $primaryKey = $pkMatch ? $pkMatch[1] : 'id';
    
    preg_match('/protected\s+\$allowedFields\s*=\s*\[(.*?)\]/s', $content, $fieldsMatch);
    $fillable = '[]';
    if ($fieldsMatch) {
        $fillable = "[\n        " . trim($fieldsMatch[1]) . "\n    ]";
    }
    
    $useTimestamps = strpos($content, "'created_at'") !== false || strpos($content, "useTimestamps = true") !== false ? "public \$timestamps = true;" : "public \$timestamps = false;";

    $eloquentContent = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$className} extends Model\n{\n    protected \$table = '{$table}';\n    protected \$primaryKey = '{$primaryKey}';\n    {$useTimestamps}\n\n    protected \$fillable = {$fillable};\n}\n";
    
    if ($className !== 'User') { // User is Authenticatable
        file_put_contents($laravelModelsPath . $className . '.php', $eloquentContent);
        echo "Generated {$className} model\n";
    }
}
