<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/3/10
 */
use yii\web\Response;
use PHPHtmlParser\Dom;
use Yii;

trait CardParser
{

    /**
     * 解析当前余额和饭卡状态
     * @param $html
     * @return array
     */
    public function parseCurrentCash($html)
    {
        if (empty($html)) return [];
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('td[class=neiwen]');
        unset($dom);
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cardNum = $contents[3]->find('div')->innerHtml;         //卡号
        $cardState = $contents[42]->find('div')->innerHtml;      //卡状态
        $freezeState = $contents[44]->find('div')->innerHtml;    //冻结状态
        $cash = explode('元',$contents[46]->innerHtml)[0];       //实时余额
        $checkState = $this->trimNbsp($contents[48]->innerHtml); //检查状态
        $lossState = $this->trimNbsp($contents[50]->innerHtml);  //挂失状态
        $item = compact(
            'cardNum','cash'
            ,'cardState','checkState','lossState','freezeState'
        );

        return $item;
    }

    //去掉&nbsp;和首尾空格
    private function trimNbsp($str){
        return str_replace('&nbsp;', '',trim($str));
    }

    /**
     * 解析校园卡消费历史（当天）
     * @param $html
     * @return array
     */
    public function parseConsumeToday($html)
    {
        if (empty($html)) return [];
        $html = iconv("GBK", "UTF-8", $html);   //GBK->UTF-8
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('table[class=dangrichaxun] tr');
        unset($dom);
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scoreList = [];
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;                              //标题头
            if (strpos($content->getAttribute('class'),'listbg') === false){
                continue;                                           //是消费记录的tr不知道什么时候结束，故这样
            }
            $time = $content->find('td', 0)->innerHtml;             //消费时间
            // $sno = $content->find('td', 1)->innerHtml;           //学号
            // $name = $content->find('td', 2)->innerHtml;          //人名
            $shop = trim($content->find('td', 4)->innerHtml);       //消费商店名 广州校区二饭堂合作方
            $change = $content->find('td', 5)->innerHtml;           //金额变动  -7.00
            $cash = $content->find('td', 6)->innerHtml;             //当前余额（消费后）
            $item = compact(
                'time','shop','change','cash'
            );
            $scoreList [] = $item;
        }
        return $scoreList;
    }

}
