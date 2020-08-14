<h1 align="center"><a href="https://pan.layne666.cn" target="_blank">OneIndex</a></h1>

> Onedrive Directory Index

## 功能

不占用服务器空间，不走服务器流量，  

直接列出 OneDrive 目录，文件直链下载。  

## 伪静态

```nginx
if (!-f $request_filename){
set $rule_0 1$rule_0;
}
if (!-d $request_filename){
set $rule_0 2$rule_0;
}
if ($rule_0 = "21"){
rewrite ^/(.*)$ /index.php?/$1 last;
}
```

## 预览图

![](https://img.layne666.cn/images/20200814134651.png)

![](http://file.layne666.cn/img/20200211210637.png)

![](http://file.layne666.cn/img/20200211210644.png)