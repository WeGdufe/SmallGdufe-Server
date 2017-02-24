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
        if(isset($this->req['split'])){
            $this->data['split'] = intval($this->req['split']);
        }else{
            $this->data['split'] = 0;
        }
        return Yii::$app->runAction('api/jw/test', $this->data);
    }
    public function actionIndex()
    {
        return 'JwController';
    }

    //http://localhost:82/index.php?r=jw/get-grade&sno=13251102210&pwd=qq5521140&stu_time=2013-2014-2
    public function actionGetGrade()
    {
        if(isset($this->req['stu_time'])){
            $this->data['stu_time'] = $this->req['stu_time'];
        }
        return Yii::$app->runAction('api/jw/get-grade', $this->data);
    }

    //http://localhost:82/index.php?r=jw/get-schedule&sno=13251102210&pwd=qq5521140
    //http://192.168.1.106:82/index.php?r=jw/get-schedule&sno=13251102217&pwd=118118&split=0&stu_time=2014-2015-1
    public function actionGetSchedule()
    {
        //没有则查询当前学期
        if(isset($this->req['stu_time'])){
            $this->data['stu_time'] = $this->req['stu_time'];
        }
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
