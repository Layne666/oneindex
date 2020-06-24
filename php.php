<?php 
$phar=new phar('app.phar');             //参数为要打包成的文件名
$phar->buildFromDirectory(__DIR__.'/','/\.php$/');  //从哪个文件夹打包 参数1为程序根目录(最好用__DIR__等魔术常量)  参数2是正则表达式(选填)表示要打包文件的后缀
$phar->compressFiles(phar::GZ);        //压缩方式
$phar->stopBuffering();            //停止缓冲
$a=$phar->createDefaultStub('./index.php'); //程序入口文件
$phar->setStub($a);