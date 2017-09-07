<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/3/10
 */
use stdClass;
use yii\web\Response;
use PHPHtmlParser\Dom;
use Yii;

trait CardParser
{

    /**
     * 解析当前余额和饭卡状态
     * @param $html
     * @return object
     */
    public function parseCurrentCash($html)
    {
        if (empty($html)) return new StdClass;
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
            if(empty($shop)){
                $shop = '充值';
            }
            $change = $content->find('td', 5)->innerHtml;           //金额变动  -7.00
            if( $change[0] != '-' &&  $change[0] != '0'){
                $change = '+'.$change;
            }
            $cash = $content->find('td', 6)->innerHtml;             //当前余额（消费后）
            $item = compact(
                'time','shop','change','cash'
            );
            $scoreList [] = $item;
        }
        return $scoreList;
    }

    public function parseElectricSims($html) {
        if (empty($html)) return [];
        //$html = iconv("GBK", "UTF-8", $html);   //GBK->UTF-8
        preg_match_all('/<table[\s\S]*?\/table>/', $html, $matches);
        $dom = new Dom;
        $dom->loadStr($matches[0][1], []);
        $elecList = [];
        $contents = $dom->find('tr');
        $cnt = count($contents);
        if($cnt <= 3) return null;
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;                              //标题头
            if($index >= $cnt - 2) break; // 结束，最后两行无效数据
            $tds = $content->find('td');
            foreach ($tds as $i => $td) {
                if($i == 2) $electric = $this->trimNbsp($td->text);// 剩余电量
                else if($i == 3) $money = $this->trimNbsp($td->text);//剩余余额
                else if($i == 6) $time = $this->trimNbsp(str_replace(".0","",$td->innerHtml));//时间
            }
            $item = compact(
                'electric','money','time'
            );
            $elecList [] = $item;
        }
        return $elecList;
    }

    public function parseElectricSdms($html) {
        if (empty($html)) return [];
        //$html = iconv("GBK", "UTF-8", $html);   //GBK->UTF-8
        $dom = new Dom;
        $dom->loadStr($html,[]);
        $elecList = [];
        $contents = $dom->find('table', 1);
        $contents = $contents->find('tr');
        $cnt = count($contents);
        foreach ($contents as $index => $content) {
            if ($index == 0) continue; //标题头
            $tds = $content->find('td');
            foreach ($tds as $i => $td) {
                if($i == 3) $electric = $this->trimNbsp($td->innerHtml);// 剩余电量
                else if($i == 5) $money = $this->trimNbsp($td->innerHtml);//剩余余额
                else if($i == 6) $time = $this->trimNbsp(preg_replace("/\.\d+/","",$td->innerHtml));//时间
            }
            $item = compact(
                'electric','money','time'
            );
            $elecList [] = $item;
        }
        return $elecList;
    }

}
