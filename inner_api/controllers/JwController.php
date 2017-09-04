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
	 * @param string $week 学期周数，格式：8（周数字），默认返回全部
     * @return array|string
     */
    public function actionGetSchedule($sno, $pwd, $stu_time = '', $split = 0, $week = '')
    {
        $jwCookie = $this->beforeBusinessAction($sno, $pwd,true);
        if (!is_array($jwCookie)) return $jwCookie;
        return $this->getReturn(Error::success, $this->getSchedule($jwCookie[0], $stu_time, $split, $week));
    }

    /**
     * 获取成绩
     * @param $sno
     * @param $pwd
     * @param string $stu_time 可选，如2014-2015-2，默认为整个大学的全部学期，2014-2015则为一个学年两个学期的成绩
     * @param int $minor 辅修为1，主修为0
     * @return array|string
     */
    public function actionGetGrade($sno, $pwd, $stu_time = '',$minor = 0)
    {
        $jwCookie = $this->beforeBusinessAction($sno, $pwd,true);
        if (!is_array($jwCookie)) return $jwCookie;
        if(strlen($stu_time) == 9){ //2014-2015这样则为查询学年成绩
            $ret = $this->getXueNianGrade($jwCookie[0], $stu_time,$minor);
        }else{
            $ret = $this->getGrade($jwCookie[0], $stu_time,$minor);
        }
        if($ret == Error::jwNotCommentTeacher){
            return $this->getReturn($ret,[]);
        }
        return $this->getReturn(Error::success,$ret);
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

    //查询一个学年（两学期）的成绩
    private function getXueNianGrade($jwCookie, $study_time = '',$minor = 0){
        $xueQi1 = $this->getGrade($jwCookie,$study_time."-1",$minor);
        if($xueQi1 == Error::jwNotCommentTeacher){
            return Error::jwNotCommentTeacher;
        }
        $xueQi2 = $this->getGrade($jwCookie,$study_time."-2",$minor);
        if($xueQi2 == Error::jwNotCommentTeacher){
            return Error::jwNotCommentTeacher;
        }
        return array_merge($xueQi1,$xueQi2);
    }

    //查成绩，未使用的功能：查询主修辅修一起算的整个大学，无参get这个地址
    //$curl->get($this->urlConst['jw']['grade']);
    private function getGrade($jwCookie, $study_time = '',$minor = 0) //都为空则为主修-整个大学
    {
        if (empty($jwCookie)) return null;
        $curl = $this->newCurl();
        $curl->setCookie($this->comCookieKey, $jwCookie);
        $curl->setReferer($this->urlConst['base']['jw']);
        $data = [
            'kksj' => $study_time,  //开课时间
            'kcxz' => '',           //课程性质
            'kcmc' => '',           //课程名称
            'fxkc' => $minor,       //辅修为1，主修为0
            'xsfs' => 'all',        //显示最好成绩(补考情况)为max【教务没显示平时分】，显示全部成绩为all
        ];
        $curl->post($this->urlConst['jw']['grade'], $data);
        //检查是否有评教
        if($this->checkHasCommentTeacher($curl->response)){
            return Error::jwNotCommentTeacher;
        }
        return $this->parseGrade($curl->response);
    }

    private function getSchedule($jwCookie, $study_time = '', $split = 0, $week = '')
    {
        if (empty($jwCookie)) return null;
        $curl = $this->newCurl();
        $curl->setCookie($this->comCookieKey, $jwCookie);
        $curl->setReferer($this->urlConst['jw']['schedule']);

        if (empty($study_time)) {
            $curl->get($this->urlConst['jw']['schedule']);
        } else {
            $data = [
				'zc' => $week,
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

    /**
     * 进行业务处理前的判断，判断学校系统崩了没，账号密码对不对
     * @param bool $isRetArray 为true则异常情况下data域返回数组 否则返回对象
     * @return array|string 各种校验通过后正常返回教务的cookie（数组），否则返回{"code":2001,"data":[]}
     */
    protected function beforeBusinessAction($sno, $pwd,$isRetArray=true)
    {
        if($isRetArray) $ret = []; //空数组
        else  $ret = new stdClass; //空对象
        if($this->isSystemCrashed($this->urlConst['base']['jw'].'/')) {
            return $this->getReturn(Error::jwSysError,$ret);
        }
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

    public function actionTest()
    {
        // echo '233';
        // var_dump($this->parseScheduleMergeNext(file_get_contents('F:\\Desktop\\233.html')));
        // Yii::$app->cache->set(self::REDIS_IDS_PRE . '13251102210', 'AQIC5wM2LY4SfcxV1CJsccnUc7vVKmuFFq904d43otL0ATU%3D%40AAJTSQACMDE%3D%23', $this->expire);
        // Yii::$app->cache->set(self::REDIS_INFO_PRE . '13251102210', '0000YHmPMyu9ZncwVmS1hq371il:18sfof8na', $this->expire);
        // echo file_get_contents('F:\\Desktop\\233.html');
        return $this->getReturn(Error::success, $this->parseGrade(file_get_contents('F:\\Desktop\\2.html')));
       //  if($this->isSystemCrashed($this->urlConst['base']['jw'].'/')) {
       //
       //      // if($this->isSystemCrashed("http://jwxt.gdufe.edu.cn/jsxsd/")){
       //     return $this->getReturn(Error::jwSysError,new stdClass);
       //
       // }else{
       //     return $this->getReturn(Error::success, $this->parseScheduleMergeNext(file_get_contents('F:\\Desktop\\new.html')));
       // }
    }

}
