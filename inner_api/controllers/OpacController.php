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
        return $this->getReturn(Error::success,$this->getSearchBook('',$bookName));
    }

    public function test()
    {

    }


    private function getSearchBook($idsCookie='',$bookName)
    {
        $curl = $this->newCurl();
        if(isset($idsCookie)) {
            // 开启登陆查询的情况
        }
        $curl->setReferer($this->urlConst['base']['opac']);
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
        $curl->get($this->urlConst['opac']['search'],$data);
        return $this->parseSearchBookList($curl->response);
    }

    private function getOpacCookie($sno, $pwd)
    {
    }


}