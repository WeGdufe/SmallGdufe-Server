<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
use yii\web\Response;
use yii;
use PHPHtmlParser\Dom;

trait InfoParser
{
    /**
     * 解析信息门户处的素拓信息 因返回的html不规范标签不闭合等 只能正则
     * @param $html
     * @return array|string
     */
    public function parseFewSztz($html)
    {
        if (empty($html)) return '';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scoreList = array();

        // $pattern = '(<td.+>(.+)<\/td>\s+){7}';
        $pattern = '<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>';
        preg_match_all('/' . $pattern . '/', $html, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $name = $matches[1][$i];
            $requireScore = $matches[6][$i];
            $score = $matches[7][$i];
            $item = ['name' => $name, 'requireScore' => $requireScore, 'score' => $score];
            $scoreList [] = $item;
        }
        return $scoreList;
    }

    public function parseS($html)
    {
        if (empty($html)) return '';
        $dom = new Dom;
        $dom->loadStr($html, []);

        $contents = $dom->find('table[id=kbtable] div');
        $scoreList = array();
        return $scoreList;
    }
}