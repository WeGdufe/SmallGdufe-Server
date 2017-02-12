<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

class InfoController  extends Controller
{

    public function actionAppLogin()
    {
        return 'InfoController';
    }

    public function actionTest()
    {
        $data = [
            'sno' => '1325',
            'pwd' => 'qq',
            'stu_time' => '2014-2015-2',
        ];
        return Yii::$app->runAction('api/info/test', $data);
    }

    //http://localhost:82/index.php?r=info/few-sztz
    public function actionFewSztz()
    {
        $data = [
            'sno' => '13251102210',
            'pwd' => 'qq5521140',
        ];
        return Yii::$app->runAction('api/info/few-sztz', $data);
    }

    //http://localhost:82/index.php?r=info/info-tips
    public function actionInfoTips()
    {
        $data = [
            'sno' => '13251102210',
            'pwd' => 'qq5521140',
        ];
        return Yii::$app->runAction('api/info/info-tips', $data);
    }
}
