<?php

namespace app\controllers;
use yii\web\Controller;
use Yii;

//不需要登陆，故不继承BaseController
class JwcController extends Controller
{

    public function actionGetXiaoli()
    {
        return Yii::$app->runAction('api/jwc/get-xiaoli');
    }

}
