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

    /**
     * 返回课程表
     * @param $sno
     * @param $pwd
     * @param string $stu_time ex. 2014-2015-2 可选，不填则返回目前所有
     * @return array|string
     */
    public function actionGetSchedule($sno, $pwd, $stu_time = '')
    {
        if (empty($sno) || empty($pwd)) {
            return $this->getReturn(Error::accountEmpty);
        }
        $cookie = $this->getJWCookie($sno, $pwd);
        if (empty($cookie)) {
            return $this->getReturn(Error::passwordError);
        }
        return $this->getReturn(Error::success,$this->getSchedule($cookie, $stu_time));
    }

    public function actionGetGrade($sno, $pwd, $stu_time = '')
    {
        if (empty($sno) || empty($pwd)) {
            return $this->getReturn(Error::accountEmpty);
        }
        $cookie = $this->getJWCookie($sno, $pwd);
        if (empty($cookie)) {
            return $this->getReturn(Error::passwordError);
        }
        return $this->getReturn(Error::success,$this->getGrade($cookie, $stu_time));
    }

    /**
     * 登陆教务系统且返回本次登陆的cookie字符串，失败返回false/~todo抛异常~
     * 登教务如果cookie不过期，则多次登陆返回的Set-Cookie是一样的
     * @param $sno
     * @param $pwd
     * @return null|string cookie
     */
    private function loginJw($sno, $pwd)
    {
        $curl = $this->newCurl();
        $data = [
            'USERNAME' => $sno,
            'PASSWORD' => $pwd,
        ];
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, false);
        $curl->post($this->urlConst['jw']['login'], $data);
        if (isset($curl->responseHeaders['Location'])) {
            return $curl->getCookie($this->comCookieKey);
        }
        return null;
    }

    /**
     * 获取教务成绩
     * @param string $jwCookie 教务系统cookie
     * @param string $study_time 学年、学期，格式：2014-2015-2 不填则返回整个大学的成绩
     * @return array json格式成绩
     */
    private function getGrade($jwCookie, $study_time = '')
    {
        if (empty($jwCookie)) return array();
        $curl = $this->newCurl();
        $curl->setCookie($this->comCookieKey, $jwCookie);
        $curl->setReferer($this->urlConst['base']['jw']);

        if (empty($study_time)) {
            $curl->get($this->urlConst['jw']['grade']);
        } else {
            $data = [
                'kksj' => $study_time,
                'kcxz' => '',
                'kcmc' => '',
                'fxkc' => '0',
                'xsfs' => 'all',
            ];
            $curl->post($this->urlConst['jw']['grade'], $data);
        }
        return $this->parseGrade($curl->response);
    }

    private function getSchedule($jwCookie, $study_time = '')
    {
        if (empty($jwCookie)) return array();
        $curl = $this->newCurl();
        $curl->setCookie($this->comCookieKey, $jwCookie);
        $curl->setReferer($this->urlConst['jw']['schedule']);

        if (empty($study_time)) {
            $curl->get($this->urlConst['jw']['schedule']);
        } else {
            $data = [
                'xnxq01id' => $study_time,
                'sfFD' => '1',
            ];
            $curl->post($this->urlConst['jw']['schedule'], $data);
        }
        return $this->parseSchedule($curl->response);
    }

    /**
     * 返回该学号对应的cookie，无则重登录以获取
     * @param $sno
     * @param $pwd
     * @return null|string cookie
     */
    private function getJWCookie($sno, $pwd)
    {
        $cache = Yii::$app->cache->get(self::REDIS_JW_PRE . $sno);
        if ($cache) {
            return $cache;
        }
        $cookie = $this->loginJw($sno, $pwd);
        if (empty($cookie)) {
            return null;
        }
        Yii::$app->cache->set(self::REDIS_JW_PRE . $sno, $cookie, $this->jwExpire);
        return $cookie;
    }

    public function actionIndex()
    {
    }

}
