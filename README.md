PHP-JWT-SZ-VERSION
=======


Installation
------------


```bash
项目文件的composer.json的require加入以下代码
"sz_jwt/sz_jwt": "dev-master"

然后执行

composer update

等待引入即可

```

Example
-------

```php
<?php

/*
 * 项目根目录的config目录创建jwt.php配置文件并配置以下内容
 */
return [
    "key" => "mountain_boat_network",//key
    "iss" => "http://local.pms.com",//签发者 可选
    "aud" => "http://local.pms.com",//接收该JWT的一方，可选
    "iat" => time(),//签发时间
    "nbf" => time(),//(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
    "exp" => mktime(23, 59, 59, date("m", time()), date("d", time()), date("Y", time())), //过期时间,当天结束时间（可修改，视项目情况而定）
    "identifier" => "username",//用户标识符，生成token时一定要带上用户标识，
    "algorithms" => "HS256",//加密方式
    "session_key" => "user_token",//token在session中的标识（可修改）
];

```

```php
<?php
//引入必要的类
use Firebase\JWT\JwtAuth;

class Index{
    
   public function index(){
       
       $data=array(
           'username'=>'xiaowu',
           'password'=>'balabalabala',
       );
       $model=new User();
       $model=$model->where('1=1');
       /*
        * 调用生成token方法说明
        * $model object 为数据模型，必传，想找用户用
        * $data array 生成token所需要的数据，一般为用户名跟密码
        * 
        * 返回值为json_encode
        * 
        */
       JwtAuth::createToken($model,$data);
       //生成后，可用tp自带session函数查看token
       dump(session(config()['jwt']['session_key']));
        
       /*
        * 验证Token
        * $token string 前端传过来的token
        * 
        * 
        * 返回值为json_encode
        * 
        */
       $token=$_SERVER['HTTP_AUTHORIZATION'];
       JwtAuth::validateToken($token);
   }
}

```
