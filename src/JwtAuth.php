<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/1/4
 * Time: 17:10
 */

namespace Sswbase\JWT;

use think\facade\Cache;

class JwtAuth extends JWT
{

    /**
     * 创建token
     * @param $model
     * @param $data
     * @return false|string
     */
    public static function createToken($model, $data)
    {
        if (empty($data) || empty($model)) {
            return json_encode(array('code' => -2, 'message' => "参数不能为空"));
        }
        //获取用户标识
        $identifier = config()['jwt']['identifier'];
        $key = config()['jwt']['key'];
        $getUser = $model->where($identifier, '=', $data[$identifier])->find();

        if (empty($getUser)) {
            return json_encode(array('code' => -2, 'message' => '无法生成token'));
        }
        $token = [
            'iss' => config()['jwt']['iss'], //签发者 可选
            'aud' => config()['jwt']['aud'], //接收该JWT的一方，可选
            'iat' => config()['jwt']['iat'], //签发时间
            'nbf' => config()['jwt']['nbf'], //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => config()['jwt']['exp'], //过期时间,这里设置2个小时
            'data' => $data
        ];
        return json_encode(array('code' => 1, 'message' => JWT::encode($token, $key)));

    }

    /**
     * 验证token
     * @param $token
     * @return false|string
     */
    public static function validateToken($token, $tokenKey, $tokenInfo)
    {

        $key = config()['jwt']['key'];
        try {
            JWT::$leeway = 60;
            $decoded = JWT::decode($token, $key, config()['jwt']['algorithms']);

//            if ($token != session(config()['jwt']['session_key'])) {
//                return json_encode(array('code' => -2, 'message' => 'token is error'));
//            }

            $getTokenUser = Cache::get($tokenInfo . ':' . $decoded->data->username);
            if (!$getTokenUser) {
                return json(array('code' => -2, 'message' => 'token not find'));
            }
            //判断token是否相同
            $getTokenInfo = Cache::get($tokenKey . ':' . $decoded->data->username);
            if ($getTokenInfo != explode(' ', $_SERVER['HTTP_AUTHORIZATION'])[1]) {
                return json(array('code' => -2, 'message' => 'token is error'));
            }

            return json_encode(array('code' => 1, 'message' => 'success'));
        } catch (SignatureInvalidException $e) {//签名不正确
            return json_encode(array('code' => -2, 'message' => $e->getMessage()));
        } catch (BeforeValidException $e) {//签名在某个时间点之后才能用
            return json_encode(array('code' => -2, 'message' => $e->getMessage()));
        } catch (ExpiredException $e) {//token过期
            return json_encode(array('code' => -2, 'message' => $e->getMessage()));
        } catch (\Exception $e) {//其他错误
            return json_encode(array('code' => -2, 'message' => $e->getMessage()));
        }
    }
}