<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */

namespace app\inner_api\controllers;
use app\inner_api\utils\CardParser;
use stdClass;
use yii;

class CardController extends InfoController
{
    const REDIS_CARD_PRE = 'card:';
    // private $comCookieKey = 'JSESSIONID';    //一卡通的cookie也是JSESSIONID
    private $cardExpire = 3600;

    const METHOD_GET = 'get';
    const METHOD_POST = 'post';

    use CardParser;

    /**
     * 当前余额和卡状态
     * {"code":0,"msg":"","data":{"cash":"152.62","cardState":"正常","checkState":"正常","lossState":"正常","freezeState":"正常"}}
     * @param $sno
     * @param $pwd
     * @return string
     */
    public function actionCurrentCash($sno, $pwd){
        $cookies = $this->beforeBusinessAction($sno, $pwd,true);
        if(!is_array($cookies))  return $cookies;
        $res = $this->getCurrentCash($cookies[0],$cookies[1]);
        Yii::info($res,'response');
        return $this->getReturn(Error::success,$res);
    }

    /**
     * 当日消费记录，需要校园卡卡号参数，可以从当日余额中获取
     * 为避免每次查记录都会去查余额，故该参数由客户端提供
     * {"code":0,"msg":"","data":[{"time":"2017/03/10 17:34:34","shop":"广州校区第二食堂","change":"-11.60","cash":"141.02"},{"time":"2017/03/10 11:34:54","shop":"广州校区二饭堂合作方","change":"-7.00","cash":"152.62"}]}
     * @param $sno
     * @param $pwd
     * @param $cardNum string|int 校园卡卡号
     * @return string
     */
    public function actionConsumeToday($sno, $pwd,$cardNum){
        if(empty($cardNum)){
            return $this->getReturn(Error::cardNumEmpty,[]);
        }
        $cookies = $this->beforeBusinessAction($sno, $pwd,true);
        if(!is_array($cookies))  return $cookies;
        return $this->getReturn(Error::success,$this->getConsumeToday($cookies[0],$cookies[1],$cardNum));
    }

    private function getCurrentCash($idsCookie,$cardCookie)
    {
        $response = $this->runCardCurl(CardController::METHOD_GET,
            $this->urlConst['card']['currentCash'], '',$idsCookie,$cardCookie);
        return $this->parseCurrentCash($response);
    }
    private function getConsumeToday($idsCookie,$cardCookie,$cardNum)
    {
        $data = [
            'account' => $cardNum,          //卡号
            'inputObject' => 'all',
            'Submit' => '+%C8%B7+%B6%A8+',  //gb2312的: 确 定
        ];
        $response = $this->runCardCurl(CardController::METHOD_POST,
            $this->urlConst['card']['consumeToday'], $data,$idsCookie,$cardCookie);
        return $this->parseConsumeToday($response);
    }

    public function actionTest()
    {
        // return $this->parseCurrentCash( file_get_contents('F:\\Desktop\\fanka_basic.html') );
        return $this->parseConsumeToday( file_get_contents('F:\\Desktop\\fanka_dangtian.html') );
    }



    /**
     * 返回校园卡系统的cookie
     * 必须访问urlConst['card']['home']才能返回正确cookie，其他页面都是返回错误cookie然后302跳错误页
     * 先获取缓存，无则用idscookie获取cardCookie，若无idscookie则返回空
     * @param $sno
     * @param $pwd string 目前不需要
     * @param $idsCookie string 必须
     * @return mixed|null
     */
    private function getCardCookie($sno,$pwd='',$idsCookie){
        $cache = Yii::$app->cache->get(self::REDIS_CARD_PRE . $sno);
        if ($cache) return $cache;
        if(empty($idsCookie))   return null;

        $curl = $this->newCurl();
        $curl->setCookie($this->idsCookieKey,$idsCookie);
        $curl->setReferer($this->urlConst['base']['info']);
        $curl->get($this->urlConst['card']['home']);
        $cardCookie = $curl->getCookie($this->comCookieKey);
        if(empty($cardCookie)) return null;
        Yii::$app->cache->set(self::REDIS_CARD_PRE . $sno, $cardCookie, $this->cardExpire);
        return $cardCookie;
    }


    //////////////////////////////////////////////
    //                  ↓工具函数↓                 //
    //////////////////////////////////////////////

    /**
     * CardAction实际操作的通用预处理，判断和获取cookie
     * @param $sno
     * @param $pwd
     * @param bool $isRetArray 返回数组还是对象
     * @return string 报错内容 |array [idsCookie,cardCookie]
     */
    protected function beforeBusinessAction($sno,$pwd,$isRetArray){
        if($isRetArray) $ret = []; //空数组
        else  $ret = new stdClass; //空对象
        if (empty($sno) || empty($pwd)) {
            return $this->getReturn(Error::accountEmpty,$ret);
        }
        $idsCookie = $this->getIdsCookie($sno,$pwd);
        $cardCookie = $this->getCardCookie($sno,$pwd,$idsCookie);
        if (empty($cardCookie)) {
            return $this->getReturn(Error::passwordError,$ret);
        }
        return [$idsCookie,$cardCookie];
    }



    /**
     * Card通用CURL代码
     * @param $method string cardController::METHOD_GET | cardController::METHOD_POST
     * @param $url
     * @param $data
     * @param $idsCookie
     * @param $cardCookie
     * @return null | string curl返回的结果
     */
    private function runCardCurl($method,$url,$data,$idsCookie,$cardCookie){
        $curl = $this->newCurl();
        if(empty($idsCookie)) {//cardCookie可以没有
            return null;
        }
        $curl->setCookie($this->idsCookieKey,$idsCookie);
        $curl->setCookie($this->comCookieKey,$cardCookie);
        $curl->setReferer($this->urlConst['base']['info']);
        if(isset($data) && is_array($data)) {
            $curl->$method($url, $data);
        }else{
            $curl->$method($url);
        }
        return $curl->response;
    }

}
