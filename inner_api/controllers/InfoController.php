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
        // $curl->follow_redirects = true;
        $curl->headers['Cookie'] = '';
        $response = $curl->post($this->urlConst['info']['login'], $data);
        preg_match('/(iPlanetDirectoryPro=.+?);/',$response->headers['Set-Cookie'],$matches);
        return $matches[1];
    }

    /**
     * 获取信息门户右边的基本信息（校园卡余额、借阅图书数等）
     * @param $sno
     * @param $pwd
     * @return mixed|null|string json格式信息（直接来源官方接口）
     */
    public function actionInfoTips($sno, $pwd){
        $idsCookie = $this->getCookie($sno,$pwd);
        return $this->getInfoTips($idsCookie,$sno);
    }

    /**
     * 获取信息门户tab标签的素拓信息
     * @param $sno
     * @param $pwd
     * @return array|null
     */
    public function actionFewSztz($sno, $pwd){
        return $this->parseFewSztz( file_get_contents('F:\\Desktop\\3.html') );
        // $idsCookie = $this->getCookie($sno,$pwd);
        // return $this->getFewSztz($idsCookie);
    }
    public function test()
    {
        $curl = $this->newCurl();
        $curl->options['CURLOPT_COOKIE'] = 'iPlanetDirectoryPro=AQIC5wM2LY4SfcxcelHi0ZcyW1NXNukLvDZ9G%2FgnNTJRlAs%3D%40AAJTSQACMDI%3D%23;dddddd=xxxxx';
        $response = $curl->get('http://localhost/1.php');
        echo $response;
    }

    private function getInfoTips($idsCookie,$sno)
    {
        if(empty($idsCookie)) return null;
        $cache = Yii::$app->cache->get(self::REDIS_INFO_PRE . $sno);
        if ($cache) {
            $idsCookie = $idsCookie . "; " . $cache;
        }
        $curl = $this->newCurl();
        $curl->options['CURLOPT_COOKIE'] = $idsCookie;
        $curl->referer = $this->urlConst['base']['info'];
        $response = $curl->post($this->urlConst['info']['tips']);

        if(isset($response->headers['Set-Cookie'])&&!empty($response->headers['Set-Cookie'])){
            $infoCookie = explode(";", $response->headers['Set-Cookie'])[0];
            Yii::$app->cache->set(self::REDIS_INFO_PRE. $sno, $infoCookie, $this->expire);
        }
        return $response->body;
    }

    private function getFewSztz($idsCookie)
    {
        if(empty($idsCookie)) return null;
        $curl = $this->newCurl();
        $curl->options['CURLOPT_COOKIE'] = $idsCookie;
        $curl->referer = $this->urlConst['base']['info'];
        $html = $curl->get($this->urlConst['info']['sztz']);
        return $this->parseFewSztz($html);
    }

    protected function getCookie($sno, $pwd)
    {
        if(empty($sno) || empty($pwd)) return '';
        $cache = Yii::$app->cache->get(self::REDIS_IDS_PRE . $sno);
        if ($cache) {
            echo "由redis获取cookie,为" . $cache . "\n\n";
            return $cache;
        }
        $strCookie = $this->loginIdsSys($sno, $pwd);
        Yii::$app->cache->set(self::REDIS_IDS_PRE . $sno, $strCookie, $this->expire);
        return $strCookie;
    }


}