<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/3
 */
use yii\web\Response;
use yii;
use PHPHtmlParser\Dom;

trait Parser
{
    public function parseGrade($html)
    {

        $dom = new Dom;
        $dom->loadStr($html,[]);
        $contents = $dom->find('table[id=dataList] tr');
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;      //标题头
            $cName = $content->find('td', 3)->innerHtml;
            $score = $content->find('td a')->innerHtml;
            $credit = $content->find('td', 5)->innerHtml;
            $item = ['name' => $cName, 'score' => $score, 'credit' => $credit];
            $scoreList [] = $item;
        }
        unset($dom);
        return $scoreList;
    }
}