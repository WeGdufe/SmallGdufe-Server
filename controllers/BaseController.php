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

        $this->req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        $this->sno = $this->req['sno'];
        $this->pwd =  $this->req['pwd'];
        $this->data = [
            'sno' => $this->sno,
            'pwd' => $this->pwd,
        ];
        Yii::warning($this->data);
        return parent::beforeAction($action);
    }

    /**
     * 缺少参数时的返回内容
     * @return object
     */
    public function getParmLeakReturn(){
        return $this->getReturn(99999,'大佬别闹');
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
