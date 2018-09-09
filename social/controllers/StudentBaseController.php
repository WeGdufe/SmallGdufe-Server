<?php
/**
 * User: xiaoguang
 * Date: 2017/11/12
 */

namespace app\social\controllers;
use app\social\base\Error;
use app\social\base\NoAuthBaseController;
use app\social\models\Student;

class StudentBaseController extends NoAuthBaseController
{

    public function actionLogin($user_id)
    {
        $model = new Student();
        $model->setAttributes($this->arrInput);
        $model->insertOrUpdate($this->arrInput['user_id']);

        $ret['token'] = $this->encryptToken('604800'.'-'.$this->arrInput['user_id']);
        $ret['user_id'] = $this->arrInput['user_id'];
        return $this->getReturn(Error::success,"登陆成功",$ret);
    }

    public function encryptToken($tokenStr){
        return str_rot13(base64_encode($tokenStr));
        // return base64_decode(str_rot13($tokenStr));  //解密
    }

    //并不需要该函数
    public function actionLogout()
    {
        // return $this->getReturn(Error::success,"",$ret);
    }

}