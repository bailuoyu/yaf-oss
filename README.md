# yaf-oss
基于yaf框架的仿阿里云oss私有静态资源服务器
***
## yaf_oss简介
1. 此源码结合了yaf框架和阿里云oss的授权原理sts，自行结合开发，目前只实现了一些常用功能，并没有做集群开发，所以只用于互联公司自身的业务需要，作为自用静态资源服务器
2. 业务服务器作为超级管理员admin，访问yaf_oss服务器，可以对其进行任何业务操作，最好是内网连接，不是内网请做好防火墙等安全措施
3. 简单介绍下sts流程，业务服务器admin获取ststoken授权，此授权低权限，低时效，返回给客户端，客户端用ststoken鉴权上传文件到yaf_oss，yaf_oss会将文件保存至缓冲区(会被定时清除)并返回结果，客户端将结果上报给业务服务器，业务服务器再与yaf_oss核对信息后将合法文件储存。
4. 通过nginx配置，在客户端上传文件时使用安全的https，访问文件时使用更加快速的http，具体配置这里不赘述。

## 一.配置环境

1. ### php环境
    1. php版本：7.0及以上，推荐版本7.2(5.5,5.6版本理论上也可以，可能需要修改部分bug)
    1. yaf扩展：2.0以上，推荐3.0以上(2.0只支持到php5.5,5.6)
    1. php.ini配置：
        1. 错误等级,推荐E_ALL & ~E_NOTICE
        1. 加入yaf配置
        ```
        [yaf]
        extension=yaf.so
        yaf.use_namespace=1
        yaf.cache_config=1
        yaf.environ=dev     #(dev,local,product根据部署环境填写)
        ```

1. ### nginx环境
    1. 版本：1.9以上,推荐1.14，低版本未尝试(apache理论上也是可以的，但未尝试)
    1. 配置,参考demo示例:
    ```
    server {
    	listen      80;
    	server_name oss.yourdomain.com;		#填写自己的域名
    	charset     utf-8;
    	
    	access_log  /usr/www/log/yourdomain-oss-access.log;
    	error_log   /usr/www/log/yourdomain-oss-error.log;
    
    
    	root    /usr/www/yaf_oss/public/;
    
    	index   index.php;
    	
    	location / {
    		##以下跨域设置
    		if ( $request_method = OPTIONS ) {
    			add_header Access-Control-Allow-Origin *;
    			add_header Access-Control-Allow-Methods GET,POST,PUT,PATCH,DELETE,OPTIONS,HEAD;
    			add_header Access-Control-Allow-Headers Origin,X-Requested-With,Content-Type,Accept,Authorization;
    			return 200;
    		}
    		##以上跨域设置
    		try_files $uri $uri/ /index.php$is_args$args;
    	}
    	
    	##静态资源公共读
    	location ~ /(public|cache/public) {
    		root /usr/www/yaf_store/;
    	}
    
    	location ~ \.php$ {
    		include fastcgi_params;
    		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    		#fastcgi_param  HTTPS	on;		#开启https标识
    		fastcgi_pass 127.0.0.1:9000;
    		try_files $uri =404;
    	}
    	
    	location ~* /\. {
    		deny all;
    	}
    	
    }
    ```

1. ### redis环境
    1. 版本：4.0以上，推荐5.0以上，低版本理论理论可行但未尝试
    1. php拓展版本：推荐3.0以上

## 二.运行环境
1. ### 配置
    1. 集成了local，dev，product三个环境，对应php.ini中的yaf.environ配置，具体请参考yaf_oss\conf\dev\application.ini
    1. yaf_oss\conf\errorcode.ini为错误代号，方便调试和查找错误，尤其是正式环境运行错误代号显得更重要，这部分也可以自己设计，更改请认真阅读相关源码。
    1. web入口文件为yaf_oss\public\index.php，cli入口文件为yaf_oss\yaf.php，可以在入口文件做一些全局操作，比如引入composer

1. ### 集成
    1. library中集成了redis，file，log等操作类，为本源码使用的功能插件，自行结合yaf开发，不足之处可以自行修改
    2. 集成了composer，但是本源码示例并未用到，web接口需要用到请打开yaf_oss\public\index.php注释的composer部分，同理，cli需要用到也可以添加引入composer部分

1. ### 运行
    1. web运行：作为示例源码，为了简单易懂并没有使用yaf的路由，即{{host}}/oss/sts/token对应的模块是**oss**，控制器是**Sts**Controller，方法是**token**Action()，实际使用中需要的话请自行加入yaf路由
    2. cli运行：由于本系统有cache文件缓冲机制，需要运行定时命令，进入yaf_oss根目录，运行php yaf.php File/Recovery/delCache清理过期文件，建议配置在linux定时任务crond中，示例：
    ```
    0 1 * * *  cd /usr/www/yaf_oss ; php yaf.php File/Recovery/delCache
    ```
    运行的对应方法是yaf_oss\application\library\command\中的**File**目录**Recovery**.php中的**delCache**()，可以仿造此模式编写其它cli脚本
    
## 三. 接口文档
详情请见api接口.md
