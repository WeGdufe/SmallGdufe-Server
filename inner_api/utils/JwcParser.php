<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/16
 */
use app\inner_api\controllers\Error;
use stdClass;
use Yii;
use yii\web\Response;
use PHPHtmlParser\Dom;

trait JwcParser
{
    /**
     * 解析校历和上课时间表，返回他们的图片地址
     * @param $html
     * @return object
     * {"timeTable":"http://jwc.gdufe.edu.cn/attach/2016/10/20/769667.jpg",
     * "xiaoLi":"http://jwc.gdufe.edu.cn/attach/2016/10/20/769666.jpg"}
     */
    public function parseXiaoLi($html)
    {
        if (empty($html)) return new stdClass;
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('div[class=detail_content_display]  img');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $item['timeTable'] = Yii::$app->params['base']['jwc'] . $contents[0]->getAttribute('src');;
        $item['xiaoLi'] = Yii::$app->params['base']['jwc'] . $contents[1]->getAttribute('src');;
        return $item;
    }

    public function parseCet($html)
    {
        if (empty($html)) return new stdClass;
        $dom = new Dom;
        $dom->loadStr($html, []);
        Yii::$app->response->format = Response::FORMAT_JSON;

        //准考证号/名字错误
        $contents = $dom->find('div[class=error]');
        if(count($contents)){
            return null;
        }

        //正常情况
        $contents = $dom->find('table[class=cetTable] td');
        $name = $contents[0]->innerHtml;
        $school = $contents[1]->innerHtml;
        $level = $contents[2]->innerHtml;
        $cetId = $contents[3]->innerHtml;
        $score = $contents[4]->find('span[class=colorRed]')->innerHtml;
        // $score = $dom->find('span[class=colorRed]')->innerHtml;
        $listenScore = $contents[6]->innerHtml;
        $readScore = $contents[8]->innerHtml;
        $writeScore = $contents[10]->innerHtml;

        $item = compact(
            'name', 'school',
            'level','cetId', 'score',
            'listenScore', 'readScore','writeScore'
        );
        foreach ($item as &$item1){
            $item1 = trim($item1);
        }
        return $item;
    }

}