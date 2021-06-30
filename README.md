<h1 align="center"><a href="https://pan.layne666.cn" target="_blank">OneIndex</a></h1>

> Onedrive Directory Index

## 功能

不占用服务器空间，不走服务器流量，直接列出 OneDrive 目录，文件直链下载。  

默认世纪互联版本，自用版本，功能看情况更新！

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

## 更新日志

1. 2021.06.30 新增功能：支持API接口上传文件（需要配置授权码）
2. 2021.06.30 修复功能：修改自用的初始化配置
3. 2021.06.14 修复功能：解决前端文件链接地址失效的问题
4. 2021.03.19 修复功能：解决列表排序问题

   ......

## API接口上传文件

* 接口地址：/api/v1/upload
* POST参数名：file
* 请求头：{"authcode":"xxxxxx"}

![image-20210630234829890](https://pan.layne666.cn/images/2021/06/30/N2PmrtkkAF.png)

![image-20210630234929694](https://pan.layne666.cn/images/2021/06/30/kCjgGPd87n.png)

## 预览图

![](https://pan.layne666.cn/images/2021/06/30/CNS0GfTf0b.png)

![](https://pan.layne666.cn/images/2021/06/30/Af0ZlBjdZE.png)

![](https://pan.layne666.cn/images/2021/06/30/2TtUwUbmgr.png)