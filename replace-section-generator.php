<?php
$modules = include __DIR__ . '/app/etc/config.php';

$packages = [];

foreach ($modules['modules'] as $moduleName => $status) {
    if ($status == 0) {
        $moduleName = str_replace('_', '', $moduleName);
        $moduleName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $moduleName));
        $packageName = 'module/'.$moduleName;
        $packages[$packageName] = "*";
    }
}

$config['replace'] = $packages;

echo \json_encode($config, JSON_UNESCAPED_SLASHES);


