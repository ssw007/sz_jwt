<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/1/4
 * Time: 17:10
 */

namespace Sswbase\JWT;
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
            return array('code' => -1, 'message' => "参数不能为空");
        }
        //获取用户标识
        $identifier = config()['jwt']['identifier'];
        $key = config()['jwt']['key'];
        $getUser = $model->where($identifier, '=', $data[$identifier])->find();

        if (empty($getUser)) {
            return array('code' => -1, 'message' => '无法生成token');
        }
        $token = [
            'iss' => config()['jwt']['iss'], //签发者 可选
            'aud' => config()['jwt']['aud'], //接收该JWT的一方，可选
            'iat' => config()['jwt']['iat'], //签发时间
            'nbf' => config()['jwt']['nbf'], //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => config()['jwt']['exp'], //过期时间,这里设置2个小时
            'data' => $data
        ];
        return array('code' => 1, 'message' => JWT::encode($token, $key));

    }

    /**
     * 验证token
     * @param $token
     * @return false|string
     */
    public static function validateToken($token)
    {

        $key = config()['jwt']['key'];
        try {
            JWT::$leeway = 60;
            JWT::decode($token, $key, config()['jwt']['algorithms']);
            return array('code' => 1, 'message' => 'success');
        } catch (SignatureInvalidException $e) {//签名不正确
            return array('code' => -1, 'message' => $e->getMessage());
        } catch (BeforeValidException $e) {//签名在某个时间点之后才能用
            return array('code' => -1, 'message' => $e->getMessage());
        } catch (ExpiredException $e) {//token过期
            return array('code' => -1, 'message' => $e->getMessage());
        } catch (\Exception $e) {//其他错误
            return array('code' => -1, 'message' => $e->getMessage());
        }
    }
}