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
     * [{"name":"算法竞赛入门经典","serial":"TP301.6/100","numAll":3,"numCan":0,"author":"刘汝佳编著","publisher":"清华大学出版社 2009","macno":"0000413900"},{"name":"算法竞赛入门经典","serial":"TP301.6/100(2D)","numAll":3,"numCan":1,"author":"刘汝佳编著","publisher":"清华大学出版社 2014","macno":"0000442705"}]
     * @param $html
     * @return array
     */
    public function parseSearchBookList($html)
    {
        if (empty($html)) return [];
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('ol[id=search_book_list] li');
        unset($dom);
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;

        $serialPattern = '{marc_no=(\d+)">(.+)?<\/a>(.+)?<\/.+?：(\d+).+?<br.+?：(\d+)<\/span>(.+)?<br />(.+)<br />}ms';
        foreach ($contents as $index => $content) {
            preg_match($serialPattern, $content, $matches);

            // NCR转码 &#x开头->utf8 注意取地址了
            foreach ($matches as &$mItem) {
                $mItem = trim($this->ncrDecode($mItem));
            }
            //有一些只有书但没入库的没有ID号，馆藏和可借都为0，如搜java
            if (empty($matches[3])) continue;

            //分割书名，注意 算法竞赛入门经典、算法竞赛入门经典.2版 区分
            $item = [
                'name' => preg_split("#\d\.#", $matches[2])[1], 'serial' => $matches[3], 'numAll' => intval($matches[4]),
                'numCan' => intval($matches[5]), 'author' => $matches[6], 'publisher' => $matches[7],
                'macno' => $matches[1],
            ];
            //macno:查看书本详细信息的id
            $scoreList [] = $item;
        }
        return $scoreList;
    }

    /**
     * 解析当前借阅书列表
     * @param $html
     * @return array
     */
    public function parseCurrentBookList($html)
    {
        if (empty($html)) return [];
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('table[class=table_line] tr');
        unset($dom);
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;      //标题头
            $barId = $content->find('td', 0)->innerHtml;                 //条码号
            $name = $content->find('td a', 0)->innerHtml;
            $author = explode('/ ', $content->find('td', 1)->innerHtml)[1];
            // $author = explode('a>', $content->find('td', 1)->innerHtml)[1];
            $borrowedTime = $content->find('td', 2)->innerHtml;          //借阅时间
            $returnTime = trim($content->find('font', 0)->innerHtml);    //归还时间
            $renewTimes = intval($content->find('td', 4)->innerHtml);            //续借次数
            $location = $content->find('td', 5)->innerHtml;

            //获取续借需要的checkId，貌似没其他作用
            $strTemp = explode(',', $content->find('div', 0)->innerHtml)[1];
            $checkId = substr($strTemp, 1, 8);

            $name = $this->ncrDecode($name);
            $author = $this->ncrDecode($author);
            $item = compact(
                'barId', 'name', 'author',
                'borrowedTime', 'returnTime',
                'renewTimes', 'location'
                , 'checkId'
            );
            $scoreList [] = $item;
        }
        return $scoreList;
    }

    /**
     * 解析历史借阅书列表
     * @param $html
     * @return array
     */
    public function parseBorrowedBookList($html)
    {
        if (empty($html)) return [];
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
            $author = $this->ncrDecode($content->find('td', 3)->innerHtml);
            $borrowedTime = $content->find('td', 4)->innerHtml;
            $returnTime = $content->find('td', 5)->innerHtml;
            $location = $content->find('td', 6)->innerHtml;
            $renewTimes = 999;    //借阅次数，因客户端统一当前借阅和历史借阅adapter导致需要
            $item = compact(
                'barId', 'name', 'author',
                'borrowedTime', 'returnTime','renewTimes', 'location'
            );
            $scoreList [] = $item;
        }
        return $scoreList;
    }

    /**
     * 解析书本详情页的借阅情况
     * 除了可借状态外都可以用库解决(不规范的</span>导致)，故可借状态单独正则匹配，再拼回去
     * [{"barId":"S1836879","serial":"TP312JA/1077","volume":"-","location":"广州校区自然科学图书区(N-Z类)","state":"可借"},{"barId":"S1836880","serial":"TP312JA/1077","volume":"-","location":"三水校区自然科学阅览区","state":"借出-应还日期：2017-04-06"}]
     * @param $html
     * @return array
     */
    public function parseBookStoreDetail($html)
    {
        if (empty($html)) return [];
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('tr[class=whitetext]');
        unset($dom);

        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            $serial = trim($content->find('td', 0)->innerHtml);
            $barId = $content->find('td', 1)->innerHtml;
            $volume = $this->trimNbsp($content->find('td', 2)->innerHtml);
            $location = trim($content->find('td', 3)->innerHtml);
            // $state = trim($content->find('td', 4)->outerHtml);   //不规范html导致报错
            $item = compact(
                'barId', 'serial', 'volume', 'location'
                // ,'state'
            );
            $scoreList [] = $item;
        }

        // 获取可借状态，同时去除可借状态中的font标签
        // <font color=green>可借</font>
        // 借出-应还日期：2017-04-06
        $index = 0;
        $pattern = '{25%"  >(.+?)</td>}';
        preg_match_all($pattern, $html, $matches);
        foreach ($matches[1] as $match) {
            $match = preg_replace("#<font.+?>#", '', $match);
            $match = preg_replace("#</font>#", '', $match);
            $scoreList[$index]['state'] = $match;
            $index++;
        }
        return $scoreList;
    }


// public function parseBookDetail($html)
// {
// }
    //去掉&nbsp;和首尾空格
    private function trimNbsp($str)
    {
        return str_replace('&nbsp;', '', trim($str));
    }

    //&#x开头->utf8
    function ncrDecode($str)
    {
        return mb_convert_encoding($str, "utf-8", 'HTML-ENTITIES');
    }
}
