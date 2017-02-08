<?php
namespace app\inner_api\controllers;
use yii\web\Controller;
use Yii;
use maxwen\yii\curl;

/**
 * User: xiaoguang
 * Date: 2017/2/3
 */
class BaseController extends Controller
{
    protected $urlConst;

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
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';

        $curl = new curl\Curl();
        $curlOptions = [
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 2,
            // 'CURLOPT_FOLLOWLOCATION' => false,
            'CURLOPT_COOKIESESSION' => true,
        ];
        $curl->options = $curlOptions;
        $curl->cookie_file = '';    //关闭库默认的cookie存文件，因多人登陆需要
        $curl->user_agent = $userAgent;
        return $curl;
    }
}