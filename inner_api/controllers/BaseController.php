<?php
namespace app\inner_api\controllers;
use yii\web\Controller;
use Yii;
use Curl\Curl;

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
            return true;
        } else {
            return false;
        }
    }

    public function newCurl()
    {
        $curl = new Curl();
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';
        $curl->setUserAgent($userAgent);
        return $curl;
    }
}