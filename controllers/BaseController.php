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
        if(isset($this->req['sno']) && isset($this->req['pwd'])) {
            $this->sno = $this->req['sno'];
            $this->pwd = $this->req['pwd'];
            if($this->sno == Yii::$app->params['schoolMateSnoFlag']){
                $this->sno = Yii::$app->params['schoolMateSno'];
                $this->pwd = Yii::$app->params['schoolMatePwd'];
            }
            $this->data = [
                'sno' => $this->sno,
                'pwd' => $this->pwd,
            ];
        }else{
            //无学号密码则赋值为空，给inner_api判断
            $this->data['sno'] = $this->data['pwd'] = '';
        }
        Yii::info($this->req,'request');
        return parent::beforeAction($action);
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
