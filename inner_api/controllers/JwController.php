<?php

namespace app\inner_api\controllers;

use stdClass;
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
     * @param string $stu_time ex. 2014-2015-2 可选，不填则返回当前学期
     * @param int $split 是否分拆连堂的课程，默认为0代表不拆，若为1则将连堂item拆成多个，避开true/false的类型问题
     * @return array|string
     */
    public function actionGetSchedule($sno, $pwd, $stu_time = '', $split = 0)
    {
        $jwCookie = $this->beforeBusinessAction($sno, $pwd,true);
        if (!is_array($jwCookie)) return $jwCookie;
        return $this->getReturn(Error::success, $this->getSchedule($jwCookie[0], $stu_time, $split));
    }

    public function actionGetGrade($sno, $pwd, $stu_time = '')
    {
        $jwCookie = $this->beforeBusinessAction($sno, $pwd,true);
        if (!is_array($jwCookie)) return $jwCookie;
        return $this->getReturn(Error::success, $this->getGrade($jwCookie[0], $stu_time));
    }

    /**
     * 返回个人信息，学院、专业、民族等
     * @param $sno
     * @param $pwd
     * @return string
     */
    public function actionGetBasic($sno, $pwd)
    {
        $jwCookie = $this->beforeBusinessAction($sno, $pwd,false);
        if (!is_array($jwCookie)) return $jwCookie;
        return $this->getReturn(Error::success, $this->getBasicInfo($jwCookie[0]));
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

    private function getBasicInfo($jwCookie)
    {
        if (empty($jwCookie)) return null;
        $curl = $this->newCurl();
        $curl->setCookie($this->comCookieKey, $jwCookie);
        $curl->setReferer($this->urlConst['base']['jw']);
        $curl->get($this->urlConst['jw']['basicInfo']);
        return $this->parseBasicInfo($curl->response);
    }

    /**
     * 获取教务成绩
     * @param string $jwCookie 教务系统cookie
     * @param string $study_time 学年、学期，格式：2014-2015-2 不填则返回整个大学的成绩
     * @return string json格式成绩
     */
    private function getGrade($jwCookie, $study_time = '')
    {
        if (empty($jwCookie)) return null;
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

    private function getSchedule($jwCookie, $study_time = '', $split = 0)
    {
        if (empty($jwCookie)) return null;
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
        if ($split) {
            return $this->parseSchedule($curl->response);
        }
        return $this->parseScheduleMergeNext($curl->response);
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

    //$isRetArray为true 则返回数组 否则返回对象
    protected function beforeBusinessAction($sno, $pwd,$isRetArray=true)
    {
        if($isRetArray) $ret = []; //空数组
        else  $ret = new stdClass; //空对象

        if (empty($sno) || empty($pwd)) {
            return $this->getReturn(Error::accountEmpty,$ret);
        }
        $jwCookie = $this->getJWCookie($sno, $pwd);
        if (empty($jwCookie)) {
            return $this->getReturn(Error::passwordError,$ret);
        }
        return [$jwCookie];
    }

    public function actionIndex()
    {
    }

    public function actionTest($split = 0)
    {
        // Yii::$app->cache->set(self::REDIS_IDS_PRE . '13251102210', 'AQIC5wM2LY4SfcxV1CJsccnUc7vVKmuFFq904d43otL0ATU%3D%40AAJTSQACMDE%3D%23', $this->expire);
        // Yii::$app->cache->set(self::REDIS_INFO_PRE . '13251102210', '0000YHmPMyu9ZncwVmS1hq371il:18sfof8na', $this->expire);
        // return $this->parseSchedule(file_get_contents('F:\\Desktop\\2.html'));
        // return $this->parseScheduleMergeNext(file_get_contents('F:\\Desktop\\2013-2014-2.html'));
        // return $this->parseScheduleMergeNext(file_get_contents('F:\\Desktop\\kb_liantang6.html'));
        return $this->getReturn(Error::success, $this->parseScheduleMergeNext(file_get_contents('F:\\Desktop\\new.html')));

        // if ($split) {
        //     return $this->getReturn(Error::success, $this->parseSchedule(file_get_contents('F:\\Desktop\\kb_liantang6.html')));
        // } else {
        //     return $this->getReturn(Error::success, $this->parseScheduleMergeNext(file_get_contents('F:\\Desktop\\kb_dasanxia.html')));
        // }

        // return '1';
        //  return $this->parseScheduleMergeNext(file_get_contents('F:\\Desktop\\kb_liantang6.html'));

        // return $this->parseBasicInfo(file_get_contents('F:\\Desktop\\4.html'));
        // return $this->getReturn(Error::success,$this->parseBasicInfo(file_get_contents('F:\\Desktop\\4.html')));

        // $idsCookie = $this->getIdsCookie('13251102210', 'qq5521140');
        // var_dump( $idsCookie);
        // $curl = $this->newCurl();
        // $curl->get('http://localhost/2.php');
        // echo $curl->getCookie($this->comCookieKey);
        // return $this->parseFewSztz( file_get_contents('F:\\Desktop\\3.html') );

    }


}
