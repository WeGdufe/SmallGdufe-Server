<?php

namespace app\inner_api\controllers;

use Yii;
use app\inner_api\utils\JwParser;

/**
 * Default controller for the `api` module
 */
class JwController extends BaseController
{
    const REDIS_JW_PRE = 'jw:';
    private $jwExpire = 1800;   //半小时
    use JwParser;

    public function actionGetSchedule($sno, $pwd, $stu_time)
    {
        // return $this->parseSchedule( file_get_contents('F:\\Desktop\\2.html') );
        $cookie = $this->getJWCookie($sno,$pwd);
        return $this->getSchedule($cookie,$stu_time);
    }

    public function actionGetGrade($sno,$pwd,$stu_time)
    {
        $cookie = $this->getJWCookie($sno,$pwd);
        return $this->getGrade($cookie,$stu_time);
    }
    //http://localhost:81/index.php?r=api/main/get-grade&sno=132511022&pwd=&stu_time=2014-2015-2
    // public function actionGetGrade()
    // {
    //     $req = yii::$app->request;
    //     $cookie = $this->getJWCookie($req->get('sno'), $req->get('pwd'));
    //     return $this->getGrade($cookie,$req->get('stu_time'));
    // }

    /**
     * 登陆教务系统且返回本次登陆的cookie字符串，失败返回false/~todo抛异常~
     * 登教务如果cookie不过期，则多次登陆返回的Set-Cookie是一样的
     * @param $sno
     * @param $pwd
     * @return
     */
    private function loginJw($sno, $pwd)
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
     * @param string $jwCookie 教务系统cookie
     * @param string $study_time 学年、学期，格式：2014-2015-2 不填则返回整个大学的成绩
     * @return array json格式成绩
     */
    private function getGrade($jwCookie, $study_time = '')
    {
        if(empty($jwCookie)) return array();
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

    private function getSchedule($jwCookie, $study_time = '')
    {
        if(empty($jwCookie)) return array();
        $curl = $this->newCurl();
        $curl->options['CURLOPT_COOKIE'] = $jwCookie;
        $curl->referer = $this->urlConst['jw']['schedule'];

        if (empty($study_time)) {
            $html = $curl->get($this->urlConst['jw']['schedule']);
        } else {
            $data = [
                'xnxq01id' => $study_time,
                'sfFD' => '1',
            ];
            $html = $curl->post($this->urlConst['jw']['schedule'], $data);
        }
        return $this->parseSchedule($html);
    }
    /**
     * 返回该学号对应的cookie，无则重登录以获取
     * @param $sno
     * @param $pwd
     * @return string cookie
     */
    private function getJWCookie($sno, $pwd)
    {
        if(empty($sno) || empty($pwd)) return '';
        $cache = Yii::$app->cache->get(self::REDIS_JW_PRE . $sno);
        if ($cache) {
            echo "由redis获取cookie,为" . $cache . "\n\n";
            return $cache;
        }
        $strCookie = $this->loginJw($sno, $pwd);
        Yii::$app->cache->set(self::REDIS_JW_PRE . $sno, $strCookie, $this->jwExpire);
        return $strCookie;
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
    public function actionIndex()
    {
    }

}
