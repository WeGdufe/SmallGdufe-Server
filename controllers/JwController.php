<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

class JwController  extends Controller
{

    public function actionIndex()
    {
        return 'JwController';
    }

    //http://localhost:81/index.php?r=jw/get-grade
    public function actionGetGrade()
    {
        $data = [
            'sno' => '13',
            'pwd' => 'qq',
            'stu_time' => '2014-2015-2',
        ];
        return Yii::$app->runAction('api/jw/get-grade', $data);
    }

    //http://localhost:81/index.php?r=jw/get-schedule
    public function actionGetSchedule()
    {
        $data = [
            'sno' => '13',
            'pwd' => 'qq',
            'stu_time' => '2014-2015-2',
        ];
        return Yii::$app->runAction('api/jw/get-schedule', $data);
    }

}