<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Faker\Provider\Base;
use Yii;
use yii\web\Controller;

class InfoController extends BaseController
{

    public function actionTest()
    {
        // $stu_time = Yii::$app->request->get('stu_time');
        // $this->data['stu_time'] = '2014-2015-2';
        return Yii::$app->runAction('api/info/test', $this->data);
    }

    //http://localhost:82/index.php?r=info/few-sztz
    public function actionFewSztz()
    {
        return Yii::$app->runAction('api/info/few-sztz', $this->data);
    }

    //http://localhost:82/index.php?r=info/info-tips
    public function actionInfoTips()
    {
        return Yii::$app->runAction('api/info/info-tips', $this->data);
    }

    //http://localhost:82/index.php?r=info/app-login&sno=13251102210&pwd=qq5521140
    public function actionAppLogin()
    {
        return Yii::$app->runAction('api/info/info-tips', $this->data);
    }
}
