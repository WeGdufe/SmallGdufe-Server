<?php
namespace app\social\base;

use stdClass;
use yii\web\Controller;
use Yii;
use Curl\Curl;
use yii\web\Response;

/**
 * User: xiaoguang
 * Date: 2017/11/12
 */
class AuthController extends BaseController
{
    protected $urlConst;
    protected $arrInput;

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        //         timestamp
// sno 学号
// token 登陆时返回的
// body
// 内容是json串
        $sign = sha1($this->arrInput['timestamp']);

        // $sign = md5($this->arrInput['timestamp'].$this->arrInput['user_id']);
        //意外情况可以返回这个强制重新登陆
        // return $this->getReturn(Error::forceLogout,"认证过期",[]);
    }

    /**
     *
     * 组成json格式返回内容
     * @param $code
     * @param string $msg
     * @param $data object|array|string 出现错误的情况填你业务对应正常返回的类型，如是空json数组[]还是空json对象{}
     * @return string json {"code":0,"data":}
     */
    public function getReturn($code,$msg='',$data=[])
    {
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'data' => [
                'code' => $code,
                'msg' => $msg,
                'data' => $data,
            ],
        ]);
    }


}