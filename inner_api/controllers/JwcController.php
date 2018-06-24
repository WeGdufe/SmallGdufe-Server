<?php

namespace app\inner_api\controllers;

use Curl\Curl;
use stdClass;
use Yii;
use yii\web\Controller;
use app\inner_api\utils\JwcParser;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class JwcController extends Controller
{
    // const REDIS_JWC_PRE = 'jwc:';
    private $jwcExpire = 1800;   //半小时
    use JwcParser;

    public function actionGetXiaoli()
    {
        return $this->getReturn(Error::success,'', $this->parseXiaoLi(''));
        //因为校历地址每次变化都是整个都变了，所以改成写死图片地址 2018-06-24 xiaoguang
        $curl = $this->newCurl();
        $curl->get(Yii::$app->params['jwc']['xiaoLi']);
        return $this->getReturn(Error::success,'', $this->parseXiaoLi($curl->response));
    }

    public function actionGetCet($zkzh='', $xm='')
    {
        if(empty($zkzh) || empty($xm)){
            return $this->getReturn(Error::cetAccountEmpty,'');
        }
        $curl = $this->newCurl();
        $data = compact(
            'zkzh', 'xm'
        );
        //待解决，执行时间长，$curl->response是空，原因不明，若在本地localhost则可用，放宽超时参数能返回带错误信息的页面
        //暂且把时间设置正常，然后直接返回自定义错误，下面两条curl都可用
        //curl 'http://www.chsi.com.cn/cet/query?zkzh=440101171107019&xm=%E9%83%AD%E5%98%89%E6%A2%81'  -H 'Accept-Encoding: deflate'  -H 'Referer: http://www.chsi.com.cn/cet'    > 1.html
        //curl 'http://www.chsi.com.cn/cet/query?zkzh=440101171107019&xm=%E9%83%AD%E5%98%89%E6%A2%81'  -H 'Accept-Encoding: gzip,deflate'  -H 'Referer: http://www.chsi.com.cn/cet/' --compressed    > 1.html

        $curl->setOpt(CURLOPT_TIMEOUT,1);
        // $curl->setOpt(CURLOPT_TIMEOUT,30);
        $curl->setOpt(CURLOPT_ENCODING,'');
        $curl->setHeader('Accept','text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8');
        $curl->setHeader('Accept-Encoding','gzip, deflate');
        $curl->setHeader('User-Agent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36');
        $curl->setHeader('Accept-Language','zh-CN,zh;q=0.8,en;q=0.6');
        $curl->setHeader('Connection','Close');
        $curl->setReferer(Yii::$app->params['base']['cet']);
        $curl->get(Yii::$app->params['jwc']['cet'], $data);
        // var_dump($curl->rawResponse);

        $res = $this->parseCet($curl->response);
        if (gettype($res) == 'string') {
            return $this->getReturn(Error::cetError, '',$res);
        }
        return $this->getReturn(Error::success, '',$res);
    }

    private function newCurl()
    {
        $curl = new Curl();
        $curl->setTimeout(3);
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';
        $curl->setUserAgent($userAgent);
        return $curl;
    }

    private function getReturn($code,$msg='', $data=null)
    {
        if ($data == null) $data = new stdClass;
        if(empty($msg)){
            $msg = Error::$errorMsg[$code];
        }
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

    public function actionIndex()
    {
    }

    public function actionTest()
    {
        return $this->parseCet( file_get_contents('F:\\Desktop\\2.html') );


    }


}
