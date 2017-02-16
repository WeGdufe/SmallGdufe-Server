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
        if (empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('ol[id=search_book_list] li');
        unset($dom);
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $serialPattern = '{\d">(.+)?<\/a>(.+)?<\/.+?：(\d+).+?<br.+?：(\d+)<\/span>(.+)?<br />(.+)<br />}ms';
        foreach ($contents as $index => $content) {
            preg_match($serialPattern, $content, $matches);

            // NCR转码 &#x开头->utf8 注意取地址了
            foreach ($matches as &$mItem) {
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

    /**
     * 解析当前借阅书列表
     * @param $html
     * @return array|string
     */
    public function parseCurrentBookList($html)
    {
        if (empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('table[class=table_line] tr');
        unset($dom);
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;      //标题头
            $barId = $content->find('td', 0)->innerHtml;
            $name = $content->find('td a', 0)->innerHtml;
            $author = explode('a>', $content->find('td', 1)->innerHtml)[1];
            $borrowedTime = $content->find('td', 2)->innerHtml;
            $returnTime = trim($content->find('font', 0)->innerHtml);
            $location = $content->find('td', 5)->innerHtml;

            $name = $this->ncrDecode($name);
            $author = $this->ncrDecode($author);
            $item = compact(
                'barId', 'name', 'author',
                'borrowedTime', 'returnTime', 'location'
            );
            $scoreList [] = $item;
        }
        return $scoreList;
    }

    /**
     * 解析历史借阅书列表
     * @param $html
     * @return array|string
     */
    public function parseBorrowedBookList($html)
    {
        if (empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('table[class=table_line] tr');
        unset($dom);
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;      //标题头
            $barId = $content->find('td', 1)->innerHtml;
            $name = $this->ncrDecode($content->find('td a', 0)->innerHtml);
            $author =  $this->ncrDecode($content->find('td', 3)->innerHtml);
            $borrowedTime = $content->find('td', 4)->innerHtml;
            $returnTime = $content->find('td', 5)->innerHtml;
            $location = $content->find('td', 6)->innerHtml;
            $item = compact(
                'barId', 'name', 'author',
                'borrowedTime', 'returnTime', 'location'
            );
            $scoreList [] = $item;
        }
        return $scoreList;
    }
    function ncrDecode($str)
    {
        return mb_convert_encoding($str, "utf-8", 'HTML-ENTITIES');
    }
}