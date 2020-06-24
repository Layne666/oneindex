<?php

$exts = ['php', 'twig'];    // 需要打包的文件后缀, twig是模版文件, 你还可以安需加入html等后缀
$dir = __DIR__;             // 需要打包的目录

$file = 'Sample.phar';      // 包的名称, 注意它不仅仅是一个文件名, 在stub中也会作为入口前缀
$phar = new Phar(__DIR__ . '/' . $file, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $file);

// 开始打包
$phar->startBuffering();

// 将后缀名相关的文件打包
foreach ($exts as $ext) {
    $phar->buildFromDirectory($dir, '/\.' . $ext . '$/');
}

// 把build.php本身摘除
$phar->delete('build.php');

// 设置入口
$phar->setStub("<?php
Phar::mapPhar('{$file}');
require 'phar://{$file}/index.php';
__HALT_COMPILER();
?>");
$phar->stopBuffering();

// 打包完成
echo "Finished {$file}\n";

