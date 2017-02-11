<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */

namespace app\inner_api\controllers;
use app\inner_api\utils\OpacParser;
use yii;

class OpacController extends InfoController
{
    // const REDIS_IDS_PRE = 'in:';
    // const REDIS_OPAC_PRE = 'op:';

    use OpacParser;

    /**
     * 根据书名进行搜索
     * @param string $sno
     * @param string $pwd
     * @param string $bookName
     * @return array|string
     */
    public function actionSearchBook($sno='', $pwd='',$bookName=''){
        // return $this->parseSearchBookList( file_get_contents('F:\\Desktop\\fdbook.html') );
        return $this->getSearchBook('',$bookName);
    }

    public function test()
    {
        // $curl = $this->newCurl();
        // $curl->options['CURLOPT_COOKIE'] = 'iPlanetDirectoryPro=AQIC5wM2LY4SfcxcelHi0ZcyW1NXNukLvDZ9G%2FgnNTJRlAs%3D%40AAJTSQACMDI%3D%23;dddddd=xxxxx';
        // $response = $curl->get('http://localhost/1.php');
        // echo $response;
    }


    private function getSearchBook($idsCookie='',$bookName)
    {
        $curl = $this->newCurl();
        if(isset($idsCookie)) {
            // 开启登陆查询的情况
        }
        $curl->referer = $this->urlConst['base']['opac'];
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
        $html = $curl->get($this->urlConst['opac']['search'],$data);
        return $this->parseSearchBookList($html->body);
    }

    private function getOpacCookie($sno, $pwd)
    {
    }


}