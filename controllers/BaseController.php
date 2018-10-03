<?php
/**
 * User: xiaoguang
 * Date: 2017/2/16
 */

namespace app\controllers;


use stdClass;
use Yii;
use yii\base\Controller;
use yii\web\Response;

//自带学号密码赋值
class BaseController extends Controller
{
    protected $sno;
    protected $pwd;
    protected $req;
    protected $data=[];

    public function beforeAction($action)
    {
        Yii::info($this->req,'request');
        return parent::beforeAction($action);
    }

    //输出json错误，并直接退出
    public function DieReturn($code,$msg,$data=null){
        if(empty($data)) $data = [];
        $ret = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        echo json_encode($ret);
        die();
    }

    public function getReturn($code,$msg,$data='')
    {
        if(empty($data)) $data = new StdClass;
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
