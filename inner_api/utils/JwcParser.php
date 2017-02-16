<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/16
 */
use Yii;
use yii\web\Response;
use PHPHtmlParser\Dom;

trait JwcParser
{
    /**
     * 解析校历和上课时间表，返回他们的图片地址
     * @param $html
     * @return null|string json example:
     * {"timeTable":"http://jwc.gdufe.edu.cn/attach/2016/10/20/769667.jpg",
     * "xiaoLi":"http://jwc.gdufe.edu.cn/attach/2016/10/20/769666.jpg"}
     */
    public function parseXiaoLi($html)
    {
        if(empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html,[]);
        $contents = $dom->find('div[class=detail_content_display]  img');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $item['timeTable'] = Yii::$app->params['base']['jwc']. $contents[0]->getAttribute('src');;
        $item['xiaoLi'] = Yii::$app->params['base']['jwc'] . $contents[1]->getAttribute('src');;
        return $item;
    }

}