<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */

namespace app\inner_api\controllers;

use app\inner_api\utils\InfoParser;
use yii;

class InfoController extends BaseController
{
    const REDIS_IDS_PRE = 'in:';
    const REDIS_INFO_PRE = 'my:';

    protected $expire = 1800;//半小时
    use InfoParser;

    /**
     * 登陆统一登陆IDS系统，获取iPlanetDirectoryPro cookie后可访问各系统
     * @param $sno
     * @param $pwd
     * @return mixed 返回cookie: iPlanetDirectoryPro
     */
    protected function loginIdsSys($sno, $pwd)
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
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, false);
        // $curl->setCookieString('');
        $curl->post($this->urlConst['info']['idsLogin'], $data);
        $idsCookie = $curl->getCookie($this->idsCookieKey);
        if($idsCookie == null){
            return null;
            //账号或密码错误
        }
        return $idsCookie;
    }

    /**
     * 获取信息门户右边的基本信息（校园卡余额、借阅图书数等）
     * @param $sno
     * @param $pwd
     * @return mixed|null|string json格式信息（直接来源官方接口）
     */
    public function actionInfoTips($sno, $pwd)
    {
        $idsCookie = $this->getIdsCookie($sno, $pwd);
        return $this->getInfoTips($idsCookie,$sno);
    }

    /**
     * 获取信息门户tab标签的素拓信息
     * @param $sno
     * @param $pwd
     * @return array|null
     */
    public function actionFewSztz($sno, $pwd)
    {
        // return $this->parseFewSztz( file_get_contents('F:\\Desktop\\3.html') );
        $idsCookie = $this->getIdsCookie($sno, $pwd);
        return $this->getFewSztz($idsCookie);
    }

    public function test()
    {

    }

    private function getInfoTips($idsCookie, $sno)
    {
        if (empty($idsCookie)) return null;
        $curl = $this->newCurl();
        $cache = Yii::$app->cache->get(self::REDIS_INFO_PRE . $sno);
        if ($cache) {
            echo "ca ".$cache."\n";
            $curl->setCookie($this->comCookieKey,$cache);
        }
        $curl->setCookie($this->idsCookieKey,$idsCookie);
        $curl->setReferer($this->urlConst['base']['info']);
        $curl->post($this->urlConst['info']['tips']);
        $infoCookie = $curl->getCookie($this->comCookieKey);
        if(null == $infoCookie){
            echo "=========不科学";
        }
        Yii::$app->cache->set(self::REDIS_INFO_PRE . $sno, $infoCookie, $this->expire);
        return $curl->response;
    }

    private function getFewSztz($idsCookie)
    {
        if (empty($idsCookie)) return null;
        $curl = $this->newCurl();
        $curl->setCookie($this->idsCookieKey,$idsCookie);
        $curl->setReferer($this->urlConst['base']['info']);
        $curl->get($this->urlConst['info']['sztz']);
        return $this->parseFewSztz($curl->response);
    }

    protected function getIdsCookie($sno, $pwd)
    {
        if (empty($sno) || empty($pwd)) return '';
        $cache = Yii::$app->cache->get(self::REDIS_IDS_PRE . $sno);
        if ($cache) {
            echo "由redis获取cookie,为" . $cache . "\n\n";
            return $cache;
        }
        $idsCookie = $this->loginIdsSys($sno, $pwd);
        Yii::$app->cache->set(self::REDIS_IDS_PRE . $sno, $idsCookie, $this->expire);
        return $idsCookie;
    }

}