<?php

namespace app\inner_api\controllers;

use maxwen\yii\curl;
use Yii;
use app\inner_api\utils\Parser;

/**
 * Default controller for the `api` module
 */
class MainController extends BaseController
{
    const REDIS_JW_PRE = 'jw:';
    private $jwExpire = 1800;   //半小时
    use Parser;

    public function actionIndex()
    {
    }

    function loginIdsSys($sno, $pwd)
    {
        $curl = $this->newCurl();
        $data = [
            'IDToken0' => '',
            'IDToken1' => $sno,
            'IDToken2' => $pwd,
            'IDButton' => 'Submit',
            'goto' => base64_encode($this->urlConst['base']['info']),
            'encoded' => 'true',
            'inputCode' => '',
            'gx_charset' => 'UTF-8',
        ];
        $response = $curl->post($this->urlConst['info']['login'], $data);
        // $response = $curl->get($this->urlConst['base']['info']);
        echo $response;
    }

    /**
     * 登陆教务系统且返回本次登陆的cookie字符串，失败返回false/~todo抛异常~
     * 登教务如果cookie不过期，则多次登陆返回的Set-Cookie是一样的
     * @param $sno
     * @param $pwd
     * @return
     */
    function loginJw($sno, $pwd)
    {
        $curl = $this->newCurl();
        $data = [
            'USERNAME' => $sno,
            'PASSWORD' => $pwd,
        ];
        $curl->follow_redirects = false;
        $curl->headers['Cookie'] = '';
        $response = $curl->post($this->urlConst['jw']['login'], $data);
        $curl->follow_redirects = true;

        switch ($response->headers['Status-Code']) {
            case 200:
                echo "登陆失败";
                return null;
            case 302:
                $headerVar = explode(";", $response->headers['Set-Cookie']);
                echo "登陆成功 cookie为" . $headerVar[0] . "\n";
                return $headerVar[0];
        }
    }

    /**
     * 获取教务成绩
     * @param $jwCookie 教务系统cookie
     * @param string $study_time 学年、学期，格式：2014-2015-2 不填则返回整个大学的成绩
     * @return array json格式成绩
     */
    public function getGrade($jwCookie, $study_time = '')
    {
        $curl = $this->newCurl();
        $curl->options['CURLOPT_COOKIE'] = $jwCookie;
        $curl->referer = $this->urlConst['base']['jw'];

        if (empty($study_time)) {
            $html = $curl->get($this->urlConst['jw']['grade']);
        } else {
            $data = [
                'kksj' => $study_time,
                'kcxz' => '',
                'kcmc' => '',
                'fxkc' => '0',
                'xsfs' => 'all',
            ];
            $html = $curl->post($this->urlConst['jw']['grade'], $data);
        }
        return $this->parseGrade($html);
    }


    //http://localhost:81/index.php?r=api/main/get-grade&sno=132511022&pwd=&stu_time=2014-2015-2
    public function actionGetGrade()
    {
        $req = yii::$app->request;
        $cookie = $this->getJWCookie($req->get('sno'), $req->get('pwd'));
        return $this->getGrade($cookie,$req->get('stu_time'));

        // $cookie = $this->getJWCookie($req->post('sno'),$req->post('pwd'));
        // return $this->getGrade($req->post('stu_time'),$cookie);
    }

    public function actionTest()
    {
        // $curl = $this->newCurl();
        $req = yii::$app->request;
        $s1 = Yii::$app->cache->get(self::REDIS_JW_PRE . $req->get('sno'));
        echo "redis获取cookie：" . $s1 . "\n";
        // return $this->getGrade($req->get('stu_time'), $s1);
    }

    /**
     * 返回该学号对应的cookie，无则重登录以获取
     * @param $sno
     * @param $pwd
     * @return 字符串 cookie
     */
    public function getJWCookie($sno, $pwd)
    {
        $cache = Yii::$app->cache->get(self::REDIS_JW_PRE . $sno);
        if ($cache) {
            echo "由redis获取cookie,为" . $cache . "\n\n";
            return $cache;
        }
        $strCookie = $this->loginJw($sno, $pwd);
        Yii::$app->cache->set(self::REDIS_JW_PRE . $sno, $strCookie, $this->jwExpire);
        return $strCookie;
    }


    public function actionIndex4()
    {
        // echo Yii::$app->cache->get('test'), "\n";
        echo Yii::$app->cache->get('test1'), "\n";

    }
    // /**
    //  * 返回json
    //  * @inheritdoc
    //  */
    // public function behaviors()
    // {
    //     return [
    //         [
    //             'class' => MainController::className(),
    //             'formats' => [
    //                 'application/json' => Response::FORMAT_JSON,
    //             ],
    //         ],
    //     ];
    // }

    function newCurl()
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';

        $curl = new curl\Curl();
        $curlOptions = [
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 2,
            // 'CURLOPT_FOLLOWLOCATION' => false,
        ];
        $curl->options = $curlOptions;
        $curl->cookie_file = '';    //关闭库默认的cookie存文件，因多人登陆需要
        $curl->user_agent = $userAgent;
        return $curl;
    }
}
