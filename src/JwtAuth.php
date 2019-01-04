<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/1/4
 * Time: 17:10
 */

namespace Firebase\JWT;


class JwtAuth extends JWT
{

    /**
     * 创建token
     * @param $model
     * @param $data
     * @return false|string
     */
    public function createToken($model, $data)
    {
        if (empty($data) || empty($model)) {
            return json_encode(array('code' => -2, 'message' => "参数不能为空"));
        }
        //获取用户标识
        $identifier = config()['jwt']['identifier'];
        $key = config()['jwt']['key'];



    }


}