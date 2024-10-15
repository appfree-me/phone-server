<?php
require(__DIR__ . '/../vendor/autoload.php');

$distDir = __DIR__ . "/../dist";
$buildDir = __DIR__ . "/../build";
$destDir = $buildDir;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/..");
$dotenv->load();

chdir(__DIR__);

system("cp -ru $distDir/* $buildDir");
chdir($distDir);
$templateFiles = array_filter(explode("\n",`find . -name "*.php"`));

foreach ($templateFiles as $file) {
    file_put_contents($destDir . "/" . substr($file, 0, strrpos($file, '.')), get_include_contents($file));
}


function get_include_contents($filename) {
    ob_start();
        require $filename;
        return ob_get_clean();
}