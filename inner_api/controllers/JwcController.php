<?php

namespace app\inner_api\controllers;

use Curl\Curl;
use Yii;
use yii\web\Controller;
use app\inner_api\utils\JwcParser;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class JwcController extends Controller
{
    const REDIS_JW_PRE = 'jwc:';
    private $jwcExpire = 1800;   //半小时
    use JwcParser;

    public function actionGetXiaoli()
    {
        $curl = $this->newCurl();
        $curl->get(Yii::$app->params['jwc']['xiaoLi']);
        return $this->getReturn(Error::success, $this->parseXiaoLi($curl->response));
    }

    public function actionGetCet($zkzh, $xm)
    {
        $curl = $this->newCurl();
        $data = compact(
            'zkzh', 'xm'
        );
        $curl->setReferer(Yii::$app->params['base']['cet']);
        $curl->get(Yii::$app->params['jwc']['cet'], $data);
        $res = $this->parseCet($curl->response);
        if (!$res) {
            return $this->getReturn(Error::cetError);
        }
        return $this->getReturn(Error::success, $res);
    }

    private function newCurl()
    {
        $curl = new Curl();
        $curl->setTimeout(3);
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';
        $curl->setUserAgent($userAgent);
        return $curl;
    }

    private function getReturn($code, $data = '')
    {
        if ($data == null) $data = '';
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

    public function actionIndex()
    {
    }

    public function actionTest()
    {
    }


}
