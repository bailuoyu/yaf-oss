[toc]

# 资源服务器yaf_oss api接口

### 环境


    
#### 本地
    
|key|value|
|---------|-------|
|`host`|http://oss.yaflocal.com|


    
#### 开发环境
    
|key|value|
|---------|-------|
|`host`|http://oss.yafdev.com|


    
#### 生产环境
    
|key|value|
|---------|-------|
|`host`|https://oss.yourdomain.com|




## 测试

    
#### 新建API
```
POST {{host}}/oss/sts/init
```


>Header

|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|    
|`Authorization`|string|1|||


> 详细说明
    
<p>111</p>


#### 测试
```
POST {{host}}/index/test
```

>Query
    
|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|
|`aaa`|string|1|||
|`bbb`|string|1|||


>Body

```json

```
|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|--|
|`file`|file|1|||



## 系统

    
#### 生成错误代号
```
GET {{host}}/system/error
```






## oss授权

    
#### sts上传文件持久化
```
POST {{host}}/oss/sts/persist
```


>Header

|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|    
|`Authorization`|string|1|鉴权token|5Syc8ePzIK0uGA2FC21VeOWATWWnuJB7EvStazGTIydmzw24jZ7OSCyOGu3IWU4D|

>Body

```json

```
|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|--|
|`sts_token`|string|1|授权的sts toekn||
|`files`|array|1|需要持久化的文件,兼容array和string逗号隔开两种格式||
|`relative_path`|string|1|指定持久化的相对路径，可以改变路径目录|ceshi2/photo|

> 详细说明
    
参数请求同时支持form-data<span style="color: rgb(34, 34, 34); font-family: Consolas, &quot;Lucida Console&quot;, &quot;Courier New&quot;, monospace; font-size: 12px; white-space: pre-wrap;">(curl post默认的提交方式)</span>和json格式的raw方式，二选一，两种同时存在时只取前者的参数<div><br></div><div>files同时支持array和string两种形式参数<br></div>

> 返回示例

```json
{
    "code": 10000,
    "msg": "success",
    "time": 1541495045,
    "data": {
        "oss_host": "http://oss.yaflocal.com",
        "persist_patch": "www/public/t1/ceshi/photo",
        "persist_files": [
            "RyReLlTlPU7bhHr8FBCEauuQ89DRHGXP.jpg"
        ]
    }
}
```
> 返回参数
    
|参数名|类型|描述|
|--|--|--|
|`data.files`|string|原值返回|
|`data.persist_files`|array|实际进行操作的文件(排除已经持久化的文件)|
#### 获取sts授权
```
POST {{host}}/oss/sts/token
```


>Header

|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|    
|`Authorization`|string|1|鉴权token|5Syc8ePzIK0uGA2FC21VeOWATWWnuJB7EvStazGTIydmzw24jZ7OSCyOGu3IWU4D|

>Body

```json

```
|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|--|
|`application`|string|1|应用:www,admin,master;common为公共文件|www|
|`ownership`|string|0|public为公共读(默认值)，private为私有读|public|
|`expire`|string|0|授权时效(秒)，默认3600,最大86400|3600|
|`relative_path`|string|1|相对路径，sts授权权限会被限制在该路径下|ceshi/photo|
|`power`|string|0|权限read,upload,move,delete;用逗号分隔的字符默认read,upload|read,upload|
|`allow_format[image]`|string|0|允许图片格式，取和预设值的交集,填all取全部预设值，默认无|all|
|`allow_format[document]`|string|0|文本文档，默认无|all|
|`allow_format[web]`|string|0|web文件，默认无|all|
|`allow_format[other]`|string|0|其它，默认无|log,rar|
|`max_size`|string|0|单个文件最大限制，以k为单位，默认20480(20M),范围10-512000(500M)|20480|
|`total_size`|string|0|累计文件空间最大限制，以k为单位，默认512000(500M),范围100-5120000(5000M)|512000|

> 详细说明
    
<div>注意通过sts授权的所上传的文件只会被资源服务器储存为临时文件(放在cahe目录中)，需要oss管理员身份再次发送请求，将临时文件转为永久文件</div><div><br></div>参数请求同时支持(form-data/<span style="color: rgb(34, 34, 34); font-family: Consolas, &quot;Lucida Console&quot;, &quot;Courier New&quot;, monospace; font-size: 12px; white-space: pre-wrap;">x-www-form-urlencoded)</span>和json格式的raw方式，二选一，两种同时存在时只取前者的参数<div><br></div><div>所有非必填参数都有默认值，不填服务器会取默认值，一般来说参数为0,'',array()时也会取默认值，参数非法时会根据具体情况报错或取默认值<br><div><br><div>allow_format为允许的各类文件格式，根据实际补充<br></div><div><br></div><div><div>允许的图片格式 'jpg,png,jpeg,gif,bmp,webp'</div><div><br></div><div>允许的文本文档格式 'txt,doc,docx,xls,xlsx,ppt,ppts,rtf'</div><div><br></div><div>允许的web文件格式 'htm,html,xml,css,js,json'</div><div><br></div><div>允许的其它文件格式 'log,rar,zip'</div></div></div></div>


## oss文件处理

    
#### 上传文件
```
POST {{host}}/oss/file/upload
```


>Header

|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|    
|`Authorization`|string|1|鉴权token||

>Body

```json

```
|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|--|
|`file`|string|1|表单文件||
|`rename`|string|0|重命名，为空或不传则不重命名||
|`application`|string|1|应用:www,admin,master;common为公共文件|admin|
|`ownership`|string|0|public为公共读(默认值)，private为私有读|public|
|`expire`|string|0|时效，默认永久，为cache时则为临时,24小时后删除||
|`tid`|string|0|厅主id，application为www,admin时必填||
|`relative_path`|string|0|相对路径||



#### oss删除文件
```
DELETE {{host}}/oss/file/delete
```

>Query
    
|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|
|`files[]`|string|1|||
|`error_level`|string|1|错误级别,当文件不存在时，为1则报错，为0则跳过||

>Header

|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|    
|`Authorization`|string|1|鉴权token|5Syc8ePzIK0uGA2FC21VeOWATWWnuJB7EvStazGTIydmzw24jZ7OSCyOGu3IWU4D|


> 详细说明
    
参数请求同时支持delete和get提交方式<div><br></div><div>files同时支持array和string两种形式参数<br></div>


## sts文件处理

    
#### 表单上传文件(单个文件)
```
POST {{host}}/sts/file/upload
```


>Header

|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|---|    
|`Authorization`|string|1|鉴权token|5rnsXBBpOahgjFRruJ0ocPelye4gBGJ3|

>Body

```json


```
|参数名|类型|必需|描述|示例|
|---------|-------|-------------|-------|--|
|`file`|file|1|表单文件||

> 详细说明
    
注意通过sts授权的所上传的文件只会被资源服务器储存为临时文件(放在cahe目录中)，需要oss管理员身份再次发送请求，将临时文件转为永久文件<div><br></div><div>客户端在上传文件后通知服务器，由服务器将文件持久化<br><div><br></div><div>参数请求使用form-data方式<br></div><div><br></div><div>此接口非常适合前端html页面，减少前端代码开发工作量</div><div><br></div><div>一次只能上传一个文件，批量上传可多次请求(理论上一个个传更好，对网络稳定性要求更低)，后续如果有需要可以增加批量上传接口</div></div>

> 返回示例

```json
{
    "code": 10000,
    "msg": "success",
    "time": 1541467253,
    "data": {
        "file_name": "RyReLlTlPU7bhHr8FBCEauuQ89DRHGXP.jpg",
        "path_name": "www/public/t1/ceshi/photo/RyReLlTlPU7bhHr8FBCEauuQ89DRHGXP.jpg",
        "cache_url": "http://oss.yaflocal.com/cache/www/public/t1/ceshi/photo/RyReLlTlPU7bhHr8FBCEauuQ89DRHGXP.jpg"
    }
}
```
> 返回参数
    
|参数名|类型|描述|
|--|--|--|
|`data.cache_url`|string|临时访问地址|
|`data.path_name`|string|持久化地址，需要文件持久化后|
