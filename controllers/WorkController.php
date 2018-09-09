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

class WorkController extends NoAuthBaseController
{

    public function actionTest()
    {
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
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());

        $feedback['sno'] = isset($req['sno']) ? $req['sno'] : '';
        $feedback['content'] = isset($req['content']) ? $req['content']  : '';
        $feedback['contact'] = isset($req['contact']) ? $req['contact']  : '';
        $feedback['fix'] = 0;
        $feedback['comment'] = '';
        $feedback['dev_brand'] = isset($req['devBrand']) ? $req['devBrand'] : '';
        $feedback['dev_model'] = isset($req['devModel']) ? $req['devModel'] : '';
        $feedback['os_version'] = isset($req['osVersion']) ? $req['osVersion'] : '';
        $feedback['app_ver'] = isset($req['appVer']) ? $req['appVer'] : '';
        // $feedback['imei'] = Yii::$app->request->get('imei');

        // $feedback['content'] = mysql_real_escape_string($feedback['content']);//有BUG会空字符串
        // $feedback['content'] = escapeshellarg($feedback['content']);
        $dt = new DateTime();
        $feedback['create_time'] = $dt->format('Y-m-d H:i:s');
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
        $res->sendFile('../release/weGdufe.apk');
    }

    /**
     * @api {post} work/get-document 文档下载
     * @apiVersion 1.0.4
     * @apiName get-document
     * @apiGroup Work
     *
     * @apiDescription 获取校历图片等文件，直接返回文件
     *
     * @apiParam {String} fileCode  文件码，目前可用值： xiaoli
     *
     */
    public function actionGetDocument()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if(!isset($req['fileCode'])) {
            return;
        }
        if($req['fileCode'] == 'xiaoli'){
            $res = \YII::$app->response;
            $res->sendFile('../release/xiaoli.jpg');
        }
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
        // if(isset($req['sno'])
            // && $req['sno'] != Yii::$app->params['schoolMateSnoFlag'] ){
            // $sno = $req['sno'];
            // Yii::$app->cache->delete('in:' . $sno);
            // Yii::$app->cache->delete('op:' . $sno);
            // Yii::$app->cache->delete('jw:' . $sno);
            // Yii::$app->cache->delete('card:' . $sno);
        // }
        return $this->getReturn(0,'',new StdClass);
    }


    private function genTestToken($sno,$pwd,$secretKey,$timestamp) {
        $str = "sno={$sno}&pwd={$pwd}";
        return $token = AesSecurity::encrypt($str,$secretKey. $timestamp);
    }

    /**
     * 给API接入时使用，生成使用连接，访问如：
     * http://127.0.0.1:82/index.php?r=work/gen-test-url&appid=哈哈&pwd=哈哈&sno=哈哈&timestamp=1536490559&secret=哈哈&from=android&app_ver=1.5.0
     */
    /**
     * @api {post} work/gen-test-url 测试URL生成
     * @apiVersion 1.1.0
     * @apiName gen-test-url
     * @apiGroup Work
     *
     * @apiDescription API接入时测试使用，用于生成关键参数进行对比，仅接入时人工访问。必备参数如下，其他业务参数请随意 <br />
     * 样例： http://api.wegdufe.com:82/index.php?r=work/gen-test-url&appid=哈哈&pwd=哈哈&sno=哈哈&timestamp=1536490559&secret=哈哈&from=android&app_ver=1.5.0
     *
     * @apiParam {String} appid        找管理员要
     * @apiParam {String} secret       密钥，找管理员要
     * @apiParam {String} timestamp    当前时间戳，实际使用时不要写死值，后端有校验有效期
     *
     * @apiSuccess {String}   token  对学号密码的加密： 对字符串 "sno=学号&pwd=密码"  进行AES(AES-128-CBC)加密（含base64），KEY 为 secret+时间戳 拼接（没有加号，样例：hello1536490559） ，最后 urlencode
     * @apiSuccess {String}   sign   参数签名： 除 r 和 sign参数以外的全部参数，组成 " appid=哈哈&parms1=哈哈 " 格式，对KEY进行字典序排序，然后 MD5
     * @apiSuccess {Object}   data      空Object
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{}}
     */
    public function actionGenTestUrl(){
        $req = $this->req;
        echo "你输入的： " . rawurldecode(http_build_query($req)) ." <br > <br >";

        $secretKey = $req['secret'];
        $timestamp = $req['timestamp'];

        //生成
        $token = $this->genTestToken($req['sno'],$req['pwd'],$secretKey,$timestamp);
        $token = urlencode($token);
        $req['token'] = $token;
        unset($req['r'],$req['sign']);
        unset($req['secret'],$req['sno'],$req['pwd']);  //测试加的干掉

//        echo json_encode($req);exit();

        ksort($req);
        $backParamsUrl = http_build_query($req);
        $backParamsUrl  = rawurldecode($backParamsUrl);    //http_build_query后需要decode
        $sign = md5($backParamsUrl);

        echo "token： " . $token ." <br > <br >";
        echo "sgin： " . $sign ." <br > <br >";
        $url = $backParamsUrl . '&sign=' . $sign;
        $url = 'http://127.0.0.1:82/index.php?r=jwc/get-xiaoli&' . $url;
//        $url = rawurldecode($url);
        $url = htmlspecialchars($url);
        echo "最终URL： " . $url ." <br > <br >";
    }


    /**
     * 输入APPID，生成SecretKey
     * @param $appId
     */
    public function genSecretKey($appId) {
        $key = md5($appId . time() . mt_rand());
        var_dump($key);exit();
    }

}
