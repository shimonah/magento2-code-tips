<?php
$modules = include __DIR__ . '/app/etc/config.php';

$packages = [];

foreach ($modules['modules'] as $moduleName => $status) {
    if ($status == 0) {
        $moduleName = str_replace('Magento_', 'Module', $moduleName);
        $moduleName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $moduleName));
        $packageName = 'magento/'.$moduleName;
        $packages[$packageName] = "*";
    }
}

$config['replace'] = $packages;

echo \json_encode($config, JSON_UNESCAPED_SLASHES);


