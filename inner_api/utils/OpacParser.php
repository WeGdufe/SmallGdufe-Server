<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/3
 */
use yii\web\Response;
use PHPHtmlParser\Dom;
use Yii;
trait OpacParser
{
    /**
     * 搜索书籍的解析 库无法处理无标签的情况，所以用正则
     * @param $html
     * @return array|string json
     */
    public function parseSearchBookList($html)
    {
        if(empty($html)) return '';
        $dom = new Dom;
        $dom->loadStr($html,[]);
        $contents = $dom->find('ol[id=search_book_list] li');
        unset($dom);
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $serialPattern = '{\d">(.+)?<\/a>(.+)?<\/.+?：(\d+).+?<br.+?：(\d+)<\/span>(.+)?<br />(.+)<br />}ms';
        foreach ($contents as $index => $content) {
            preg_match($serialPattern,$content,$matches);

            // NCR转码 &#x开头->utf8 注意取地址了
            foreach ($matches as &$mItem){
                $mItem = trim($this->ncrDecode($mItem));
            }
            $item = [
                'name' => $matches[1], 'serial' => $matches[2], 'numAll' => $matches[3],
                'numCan' => $matches[4], 'author' => $matches[5], 'publisher' => $matches[6],
            ];
            $scoreList [] = $item;
        }
        return $scoreList;
    }

    function ncrDecode($str){
        return mb_convert_encoding($str , "utf-8", 'HTML-ENTITIES');
    }
}