<?php

namespace app\controllers;
use yii\web\Controller;
use Yii;

//不需要登陆，故不继承BaseController
class JwcController extends Controller
{
    private $req;
    private $data;
    public function actionGetXiaoli()
    {
        return Yii::$app->runAction('api/jwc/get-xiaoli');
    }
    public function actionGetCet()
    {
        $this->req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        $this->data['zkzh'] = $this->req['zkzh'];
        $this->data['xm'] = $this->req['xm'];
        return Yii::$app->runAction('api/jwc/get-cet', $this->data);
    }
}
