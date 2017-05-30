<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use app\models\Feedback;
use DateTime;
use Faker\Provider\Base;
use stdClass;
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
     * @apiVersion 1.0.3
     * @apiName feedback
     * @apiGroup Work
     *
     * @apiDescription 反馈，将存入服务器数据库
     *
     * @apiParam {String} sno       学号
     * @apiParam {String} content   反馈内容
     * @apiParam {String} contact   联系方式
     * @apiParam {String} devBrand   手机品牌，可选
     * @apiParam {String} devModel   手机型号，可选
     * @apiParam {String} osVersion   操作系统版本号，可选
     *
     * @apiSuccess {int}      code      状态码，为0
     * @apiSuccess {String}   msg       错误信息，此处一定为空
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

        $feedback['fix'] = 0;

        $feedback['devBrand'] = Yii::$app->request->get('devBrand');
        $feedback['devModel'] = Yii::$app->request->get('devModel');
        $feedback['osVersion'] = Yii::$app->request->get('osVersion');
        // $feedback['imei'] = Yii::$app->request->get('imei');

        // $feedback['content'] = mysql_real_escape_string($feedback['content']);//有BUG会空字符串
        // $feedback['content'] = escapeshellarg($feedback['content']);
        $dt = new DateTime();
        $feedback['createTime'] = $dt->format('Y-m-d H:i:s');
        if( $feedback['sno'] == Yii::$app->params['schoolMateSnoFlag'] ){
            $feedback['sno'] = '88888888888';
        }

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

    /**
     * @api {post} work/get-app-tips 获取每日启动Tips
     * @apiVersion 1.0.1
     * @apiName get-app-tips
     * @apiGroup Work
     * @apiDescription 获取每日启动Tips内容
     *
     * @apiSuccess {int}      code      状态码，为0
     * @apiSuccess {String}   msg       错误信息，此处一定为空
     * @apiSuccess {Object}   data      基本信息
     * @apiSuccess {String}     data.version             Tips版本，用于区分Tips
     * @apiSuccess {boolean}    data.enable              是否启用
     * @apiSuccess {String}     data.title               Tips标题
     * @apiSuccess {String}     data.message             Tips内容
     * @apiSuccess {String}     data.startTime           Tips有效时间（开始），格式：yyyy-MM-dd HH:mm:ss
     * @apiSuccess {String}     data.endTime             Tips有效时间（截止）
     * @apiSuccess {String}     data.openUrl             http等浏览器支持的地址，非空的情况会在app多一个按钮用于调用浏览器打开
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{"version":1,"enable":true,"title":"你的反馈是我更新的动力","message":"有功能需求的话可以提下反馈，不过近期毕设答辩，会晚一点更新，另无薪招聘技术大佬呀~","startTime":"2017-04-17 20:58:22","endTime":"2017-04-17 22:59:28","openUrl":"http://www.wintercoder.com:8080/"}}
     *
     */
    public function actionGetAppTips()
    {
        return $this->getReturn(0,'',Yii::$app->params['appTips']);
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

    /**
     * @api {post} work/all-logout 彻底退出登录
     * @apiVersion 1.0.2
     * @apiName all-logout
     * @apiGroup Work
     *
     * @apiSuccess {int}      code      状态码，为0
     * @apiSuccess {String}   msg       错误信息，此处一定为空
     * @apiSuccess {Object}   data      空Object
     *
     * @apiParam   {String}   sno       学号
     *
     * @apiDescription 清空服务器中该账号的缓存，达到彻底退出登录
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{}}
     */
    public function actionAllLogout()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if(isset($req['sno'])
            && $req['sno'] != Yii::$app->params['schoolMateSnoFlag'] ){
            $sno = $req['sno'];
            Yii::$app->cache->delete('in:' . $sno);
            Yii::$app->cache->delete('op:' . $sno);
            Yii::$app->cache->delete('jw:' . $sno);
            Yii::$app->cache->delete('card:' . $sno);
        }
        return $this->getReturn(0,'',new StdClass);
    }



    private function getReturn($code,$msg,$data='')
    {
        if(empty($data)) $data = new StdClass;
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'data' => [
                'code' => $code,
                'msg' => $msg,
                'data' => $data,
            ],
        ]);
    }
}
