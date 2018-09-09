<?php
/**
 *
 * User: wintercoder
 * Date: 2018/9/9
 * AES工具类（PHP7版）
 */


namespace app\controllers;

class AesSecurity
{
    /**
     * @param $input string 原数据
     * @param $key string key
     * @return string 加密密文
     */
    public static function encrypt($input, $key)
    {
        $data  =  openssl_encrypt($input, 'AES-128-CBC',$key,0,$key);
//        $data = base64_encode($data);
        return $data;
    }

    /**
     * @param $input string 加密密文
     * @param $key string key
     * @return string 解密
     */
    public static function decrypt($input, $key)
    {
        $decrypted = openssl_decrypt($input, 'AES-128-CBC', $key, 0, $key);
        return $decrypted;
    }
}