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
        // return \Yii::createObject([
        //     'class' => 'yii\web\Response',
        //     'format' => Response::FORMAT_JSON,
        //     'data' => Yii::$app->params['update'],
        // // ]);
        // $res = \YII::$app->response;
        // $res->sendFile('../apidoc/index.html');
        // return $this->renderFile('../apidoc/index.html');
    }

    //http://localhost:82/index.php?r=work/feedback&sno=13251102210&content=%E6%B5%8B%E8%AF%95&email=&phone=15692006775
    /**
     * @api {post} work/feedback 反馈
     * @apiVersion 1.0.0
     * @apiName feedback
     * @apiGroup Work
     *
     * @apiDescription 反馈，将存入服务器数据库
     *
     * @apiParam {String} sno       学号
     * @apiParam {String} content   反馈内容
     * @apiParam {String} contact   联系方式
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}   data      空Object
     *
     * @apiError 1002 反馈内容太长啦
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{}}
     */
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
            return '{"code":0,"msg":"","data":{}}';
        }
        return '{"code":1002,"msg":"反馈内容太长啦","data":{}}';
    }

    //http://localhost:82/index.php?r=work/check-app-update
    /**
     * @api {post} work/check-app-update 检查更新
     * @apiVersion 1.0.0
     * @apiName check-app-update
     * @apiGroup Work
     *
     * @apiDescription 检查更新，返回最新版更新信息和下载地址
     *
     * @apiSuccess {String}     original            预留的额外信息，目前未使用
     * @apiSuccess {boolean}    forced              是否强制更新
     * @apiSuccess {String}     updateContent       更新提示信息
     * @apiSuccess {String}     updateUrl           apk下载地址
     * @apiSuccess {long}       updateTime          新版发布时间戳
     * @apiSuccess {int}        versionCode         版本号（内部使用）
     * @apiSuccess {String}     versionName         版本名，给用户看的
     * @apiSuccess {boolean}    ignore              是否可忽略该版本
     *
     * @apiSuccessExample {json} 正常返回
     * {"original":"","forced":false,"updateContent":"1.实现Dr.com\n2.增加自动更新\n3.实现头像图标\n","updateUrl":"http://www.wintercoder.com:82/index.php?r=work/update","updateTime":1490270887,"versionCode":1,"versionName":"1.0.0","ignore":false}
     */
    public function actionCheckAppUpdate()
    {
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'data' => Yii::$app->params['update'],
        ]);
    }

    //http://localhost:82/index.php?r=work/update
    /**
     * @api {post} work/update 最新版apk下载
     * @apiVersion 1.0.0
     * @apiName update
     * @apiGroup Work
     *
     * @apiDescription 返回最新版apk文件，访问一下就能下载
     *
     */
    public function actionUpdate()
    {
        $res = \YII::$app->response;
        $res->sendFile('../release/app-release.apk');
    }


}
