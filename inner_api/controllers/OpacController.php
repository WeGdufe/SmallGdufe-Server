<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */

namespace app\inner_api\controllers;
use app\inner_api\utils\OpacParser;
use stdClass;
use yii;

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
     * @param string $bookName   必须
     * @return array|string
     */
    public function actionSearchBook($sno='', $pwd='',$bookName=''){
        if(empty($bookName)){
            return $this->getReturn(Error::opacBookEmpty,[]);
        }
        return $this->getReturn(Error::success,$this->getSearchBook($bookName));
    }
    public function actionCurrentBook($sno, $pwd){
        $cookies = $this->beforeBusinessAction($sno, $pwd,true);
        if(!is_array($cookies))  return $cookies;
        return $this->getReturn(Error::success,$this->getCurrentBook($cookies[0],$cookies[1]));
    }
    public function actionBorrowedBook($sno, $pwd){
        $cookies = $this->beforeBusinessAction($sno, $pwd,true);
        if(!is_array($cookies))  return $cookies;
        return $this->getReturn(Error::success,$this->getBorrowedBook($cookies[0],$cookies[1]));
    }


    public function actionTest()
    {
        // return $this->parseSearchBookList( file_get_contents('F:\\Desktop\\ces.html') );
        return $this->parseCurrentBookList( file_get_contents('F:\\Desktop\\curbook.html') );
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
     * @param $data
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
