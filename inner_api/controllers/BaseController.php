<?php
namespace app\inner_api\controllers;

use stdClass;
use yii\web\Controller;
use Yii;
use Curl\Curl;
use yii\web\Response;

/**
 * User: xiaoguang
 * Date: 2017/2/3
 */
class BaseController extends Controller
{
    protected $urlConst;
    protected $comCookieKey = 'JSESSIONID';
    protected $idsCookieKey = 'iPlanetDirectoryPro';

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->urlConst = Yii::$app->params;
            // Yii::warning("访问");
            return true;
        } else {
            return false;
        }
    }

    /**
     * get一下各系统首页看是不是系统崩溃了，通常在beforeBusinessAction里被调用
     * 教务系统崩溃了返回true
     * @param $url string 首页地址，注意地址最后有没有/，如教务就需要有，否则跳302
     * @return bool
     */
    protected function isSystemCrashed($url){
        $curl = $this->newCurl();
        $curl->setTimeout(1);
        $curl->setConnectTimeout(1);
        $curl->setOpt(CURLOPT_NOBODY,true);
        $curl->get($url);
        if($curl->error) return true;       //响应超时之类，不过貌似404也被归到这里？
        $httpCode = $curl->getInfo(CURLINFO_HTTP_CODE);
        if($httpCode != 200 && $httpCode != 302){  //404等情况也认为是系统崩溃
            return true;
        }
        return false;
    }
    /**
     * 各业务网站根据自己需求的处理cookie和账号密码判异常部分
     * 在实际发起业务get/post之前调用
     * @param $sno
     * @param $pwd
     * @param bool $isRetArray  为1则失败的时候返回空数组[]，为0则返回空对象{}
     */
    protected function beforeBusinessAction($sno,$pwd,$isRetArray){
    }

    public function newCurl()
    {
        $curl = new Curl();
        $curl->setTimeout(3);
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';
        $curl->setUserAgent($userAgent);
        return $curl;
    }

    /**
     *
     * 组成json格式返回内容
     * @param $code
     * @param $data object|array|string 出现错误的情况填你业务对应正常返回的类型，如是空json数组[]还是空json对象{}
     * @return string json {"code":0,"data":}
     */
    public function getReturn($code,$data)
    {
        // if($data == null) $data = [];
        // if( $data == null) $data = new stdClass;
        if(!isset($data)) $data = new stdClass;
        $msg = Error::$errorMsg[$code];
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