<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */

namespace app\inner_api\controllers;
use app\inner_api\utils\OpacParser;
use stdClass;
use Yii;

class OpacController extends InfoController
{
    const REDIS_OPAC_PRE = 'op:';
    private $opacCookieKey = 'PHPSESSID';
    private $opacExpire = 3600; //web上是一年

    const METHOD_GET = 'get';
    const METHOD_POST = 'post';

    use OpacParser;

    /**
     * 根据书名进行搜索
     * @param string $sno
     * @param string $pwd
     * @param string $bookName 必须
     * @return array|string
     */
    public function actionSearchBook($sno = '', $pwd = '', $bookName = '')
    {
        if (empty($bookName)) {
            return $this->getReturn(Error::opacBookEmpty, []);
        }
        return $this->getReturn(Error::success, $this->getSearchBook($bookName));
    }

    public function actionCurrentBook($sno, $pwd)
    {
        $cookies = $this->beforeBusinessAction($sno, $pwd, true);
        if (!is_array($cookies)) return $cookies;
        return $this->getReturn(Error::success, $this->getCurrentBook($cookies[0], $cookies[1]));
    }

    public function actionBorrowedBook($sno, $pwd)
    {
        $cookies = $this->beforeBusinessAction($sno, $pwd, true);
        if (!is_array($cookies)) return $cookies;
        return $this->getReturn(Error::success, $this->getBorrowedBook($cookies[0], $cookies[1]));
    }

    public function actionRenewBook($sno, $pwd, $barId, $checkId, $verify)
    {
        //参数不完整
        if (!isset($barId) || !isset($checkId) || !isset($verify)
            || empty($barId) || empty($checkId) || empty($verify)
        ) {
            return $this->getReturn(Error::opacRenewParmEmpty, new StdClass);
        }
        $cookies = $this->beforeBusinessAction($sno, $pwd, true);
        if (!is_array($cookies)) return $cookies;
        return $this->getReturn(Error::success, $this->doRenewBook($cookies[0], $cookies[1], $barId, $checkId, $verify));
    }

    //获取续借的验证码图片，需登陆状态才有效
    public function actionGetRenewBookVerify($sno, $pwd)
    {
        $cookies = $this->beforeBusinessAction($sno, $pwd, true);
        if (!is_array($cookies)) return $cookies;
        return $this->getReturn(Error::success, $this->getRenewBookVerifyCode($cookies[0], $cookies[1]));
    }

    /**
     * 获取书本借阅情况
     * @param $macno
     * @return string
     */
    public function actionGetBookStoreDetail($macno){
        if (empty($macno)) {
            return $this->getReturn(Error::opacBookDetailIdEmpty, []);
        }
        return $this->getReturn(Error::success, $this->getBookStoreDetail($macno));
    }



    public function actionTest()
    {
        // return $this->parseSearchBookList( file_get_contents('F:\\Desktop\\ces.html') );
        return $this->parseBookStoreDetail( file_get_contents('F:\\Desktop\\2333.html') );
        // return $this->parseHistoryBorrowedBookList( file_get_contents('F:\\Desktop\\bo.html') );
    }

    private function getCurrentBook($idsCookie,$opacCookie)
    {
        $response = $this->runOpacCurl(OpacController::METHOD_GET,
            $this->urlConst['opac']['currentBook'], '',$idsCookie,$opacCookie);
        return $this->parseCurrentBookList($response);
    }
    private function getBorrowedBook($idsCookie,$opacCookie)
    {
        $response = $this->runOpacCurl(OpacController::METHOD_GET,
            $this->urlConst['opac']['borrowedBook'], '',$idsCookie,$opacCookie);
        return $this->parseBorrowedBookList($response);
    }
    private function getRenewBookVerifyCode($idsCookie,$opacCookie)
    {
        $response = $this->runOpacCurl(OpacController::METHOD_GET,
            $this->urlConst['opac']['renewBookVerify'], '',$idsCookie,$opacCookie);
        $ret['data'] = base64_encode($response);
        return $ret;
    }
    private function getBookStoreDetail($macno)
    {
        $curl = $this->newCurl();
        $data = [
            'marc_no' => $macno,
        ];
        $curl->setReferer($this->urlConst['base']['opac']);
        $curl->get($this->urlConst['opac']['bookDetail'],$data);
        return $this->parseBookStoreDetail($curl->response);
    }
    // 搜索(不登录状态)
    private function getSearchBook($bookName,$idsCookie='',$opacCookie='')
    {
        $curl = $this->newCurl();
        $data = [
            's2_type' => 'title',
            's2_text' => $bookName,
            'search_bar' => 'new',
            'title' => $bookName,
            'doctype' => 'ALL',
            'with_ebook' => 'on',
            'match_flag' => 'forward',
            'showmode' => 'list',
            'location' => 'ALL',
        ];
        $curl->setReferer($this->urlConst['base']['opac']);
        $curl->get($this->urlConst['opac']['search'],$data);
        return $this->parseSearchBookList($curl->response);
    }
    /**
     * 实际发起http请求去 续借图书
     * @param $idsCookie
     * @param $opacCookie
     * @param $barId string 条形码
     * @param $checkId string 不知道什么东西，在当前借阅页面里可获取
     * @param $verify string 验证码，通常由客户端输入提供，验证码从actionGetRenewBookVerify获取
     * @return mixed
     */
    private function doRenewBook($idsCookie,$opacCookie,$barId,$checkId,$verify)
    {
        $data = [
            'bar_code' => $barId,
            'captcha' => $verify,       //验证码
            'check' => $checkId,        //续借用的码
            'time' => $this->getMillisecond(),
        ];
        $response = $this->runOpacCurl(OpacController::METHOD_GET,
            $this->urlConst['opac']['renewBook'], $data,$idsCookie,$opacCookie);

        //<font color=green>续借成功</font>
        //<font color=red>超过最大续借次数，不得续借！</font>
        //错误的验证码(wrong check code)
        $pattern = '#.+?>(.+?)<\/font>#';
        if(preg_match($pattern, $response, $matches)){
            $ret['data'] = $matches[1];
        }else{
            $ret['data'] = $response;
        }
        return $ret;
    }

    /**
     * 返回图书馆系统的cookie
     * 先获取缓存，无则用idscookie获取opacCookie，若无idscookie则返回空
     * @param $sno
     * @param $pwd string 目前不需要
     * @param $idsCookie string 必须
     * @return mixed|null
     */
    private function getOpacCookie($sno,$pwd='',$idsCookie){
        $cache = Yii::$app->cache->get(self::REDIS_OPAC_PRE . $sno);
        if ($cache) return $cache;
        if(empty($idsCookie))   return null;

        $curl = $this->newCurl();
        $curl->setCookie($this->idsCookieKey,$idsCookie);
        $curl->setReferer($this->urlConst['base']['info']);
        $curl->get($this->urlConst['opac']['login']);
        $opacCookie = $curl->getCookie($this->opacCookieKey);
        if(empty($opacCookie)) return null;
        Yii::$app->cache->set(self::REDIS_OPAC_PRE . $sno, $opacCookie, $this->opacExpire);
        return $opacCookie;
    }


    //////////////////////////////////////////////
    //                  ↓工具函数↓                 //
    //////////////////////////////////////////////
    /**
     * 返回毫秒级的时间戳（float类型的纯整数，无小数点），位数更多
     * @return float
     */
    private function getMillisecond(){
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

    /**
     * OPAC图书馆Action实际操作的通用预处理，判断和获取cookie
     * @param $sno
     * @param $pwd
     * @param bool $isRetArray 返回数组还是对象
     * @return string 报错内容 |array [idsCookie,opacCookie]
     */
    protected function beforeBusinessAction($sno,$pwd,$isRetArray){
        if($isRetArray) $ret = []; //空数组
        else  $ret = new stdClass; //空对象
        if (empty($sno) || empty($pwd)) {
            return $this->getReturn(Error::accountEmpty,$ret);
        }
        $idsCookie = $this->getIdsCookie($sno,$pwd);
        $opacCookie = $this->getOpacCookie($sno,$pwd,$idsCookie);
        if (empty($opacCookie)) {
            return $this->getReturn(Error::passwordError,$ret);
        }
        return [$idsCookie,$opacCookie];
    }
    /**
     * OPAC的通用CURL代码
     * @param $method string OpacController::METHOD_GET | OpacController::METHOD_POST
     * @param $url
     * @param $data array 参数
     * @param $idsCookie
     * @param $opacCookie
     * @return null | string curl返回的结果
     */
    private function runOpacCurl($method,$url,$data,$idsCookie,$opacCookie){
        $curl = $this->newCurl();
        if(empty($opacCookie)) {//idsCookie可以没有
            return null;
        }
        $curl->setCookie($this->idsCookieKey,$idsCookie);
        $curl->setCookie($this->opacCookieKey,$opacCookie);
        $curl->setReferer($url);
        if(isset($data) && is_array($data)) {
            $curl->$method($url, $data);
        }else{
            $curl->$method($url);
        }
        return $curl->response;
    }


}
