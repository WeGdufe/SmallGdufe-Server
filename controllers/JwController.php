<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Faker\Provider\Base;
use Yii;
use yii\web\Controller;

class JwController  extends BaseController
{


    public function actionTest()
    {
        $this->data['stu_time'] = '2014-2015-2';
        return Yii::$app->runAction('api/jw/test', $this->data);
    }
    public function actionIndex()
    {
        return 'JwController';
    }

    //http://localhost:82/index.php?r=jw/get-grade
    public function actionGetGrade()
    {
        $this->data['stu_time'] = '2014-2015-2';
        return Yii::$app->runAction('api/jw/get-grade', $this->data);
    }

    //http://localhost:82/index.php?r=jw/get-schedule&sno=13251102210&pwd=qq5521140
    //http://localhost:82/index.php?r=jw/get-schedule&sno=13251102210&pwd=qq5521140&split=1
    public function actionGetSchedule()
    {
        $this->data['stu_time'] = '2014-2015-2';
        if(isset($this->req['split'])){
            $this->data['split'] = intval($this->req['split']);
        }else{
            $this->data['split'] = 0;
        }
        return Yii::$app->runAction('api/jw/get-schedule', $this->data);
    }
    public function actionGetBasic()
    {
        return Yii::$app->runAction('api/jw/get-basic', $this->data);
    }


}
