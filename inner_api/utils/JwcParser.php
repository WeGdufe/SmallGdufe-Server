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
        $item = [];
        $item['timeTable'] = 'http://jwc.gdufe.edu.cn/_upload/article/images/d1/1f/b2ddfd984948a84b467cb1484887/71b55fd0-faa9-42d5-979f-272fb39f8424.jpg';
        $item['xiaoLi'] = 'http://jwc.gdufe.edu.cn/_upload/article/images/d1/1f/b2ddfd984948a84b467cb1484887/b47e8477-a67f-4826-9706-c3c5da86d61c.jpg';
        return $item;
        
        //因为校历地址每次变化都是整个都变了，所以改成写死图片地址 2018-06-24 xiaoguang
        if (empty($html)) return new stdClass;
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('div[class=detail_content_display]  img');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $item['timeTable'] = Yii::$app->params['base']['jwc'] . $contents[0]->getAttribute('src');
        $item['xiaoLi'] = Yii::$app->params['base']['jwc'] . $contents[1]->getAttribute('src');
    }

    public function parseCet($html)
    {
        if (empty($html)) return "Sorry，服务器智障了，待修复"; // new stdClass;

        $dom = new Dom;
        $dom->loadStr($html, []);
        Yii::$app->response->format = Response::FORMAT_JSON;

        //准考证号/名字错误
        $contents = $dom->find('div[class=error]');
        if(count($contents)){   //返回报错信息
            return trim($contents->innerHtml);
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