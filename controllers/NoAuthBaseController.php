<?php
/**
 * 不验证登录的基类
 * User: xiaoguang
 * Date: 2018/09/09
 */

namespace app\controllers;


use stdClass;
use Yii;
use yii\base\Controller;
use yii\web\Response;

class NoAuthBaseController extends BaseController
{
    protected $sno;
    protected $pwd;
    protected $req;
    protected $data=[];

    public function beforeAction($action)
    {
        $this->req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        return parent::beforeAction($action);
    }

}
