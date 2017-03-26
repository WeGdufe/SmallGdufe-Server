<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use app\models\Feedback;
use Faker\Provider\Base;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class WorkController extends Controller
{

    public function actionTest()
    {
        // $stu_time = Yii::$app->request->get('stu_time');
        // $this->data['stu_time'] = '2014-2015-2';
        // return Yii::$app->runAction('api/info/test', $this->data);
    }

    //http://localhost:82/index.php?r=work/feedback&sno=13251102210&content=%E6%B5%8B%E8%AF%95&email=&phone=15692006775
    public function actionFeedback()
    {
        $feedback = new Feedback();
        $feedback['sno'] = Yii::$app->request->get('sno');
        $feedback['content'] = Yii::$app->request->get('content');
        $feedback['contact'] = Yii::$app->request->get('contact');
        // $feedback['phone'] = Yii::$app->request->get('phone');
        // $feedback['content'] = mysql_real_escape_string($feedback['content']);
        $feedback['content'] = escapeshellarg($feedback['content']);
        if (strlen($feedback['content']) < 1000) {
            $feedback->save(false);
        }
        return '{"code":0,"msg":"","data":{}}';
    }

    //http://localhost:82/index.php?r=work/check-app-update
    public function actionCheckAppUpdate()
    {
        // var_dump(Yii::$app->params['update']);
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'data' => Yii::$app->params['update'],
        ]);
    }
    //http://localhost:82/index.php?r=work/update
    public function actionUpdate()
    {
        $res = \YII::$app->response;
        $res->sendFile('../release/app-release.apk');
    }


}
