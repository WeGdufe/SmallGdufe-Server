<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
use yii\web\Response;
use yii;
use PHPHtmlParser\Dom;

/**
 * 信息门户HTML解析器，返回均json格式
 * Class InfoParser
 * @package app\inner_api\utils
 */
trait InfoParser
{
    /**
     * 解析信息门户处的素拓信息 因返回的html不规范标签不闭合等 只能正则
     * 16级的在系统上最低学分为0.0，直接输出$scoreArr，数据是信院公众号2017.4.18发的
     * @param $html
     * @return array
     */
    public function parseFewSztz($html)
    {
        if (empty($html)) return [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $nameArr = ['身心素质','文化艺术素质','技能素质','思想品德素质','创新创业素质','任选'];
        $scoreArr = ['1.5','1.5','1.5','2.0','2.5','1.0'];
        $scoreList = array();
        // $pattern = '<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>\s+<td.+>(.+)<\/td>';
        // $pattern = '(<td.+>(.+)<\/td>\s+){7}';   //这招不可行，无解故用for https://www.v2ex.com/t/339115
        $pattern = '';
        for ($i = 0; $i < 7; $i++) {
            $pattern .= '<td.+>(.+)<\/td>\s+';
        }
        preg_match_all('/' . $pattern . '/', $html, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $name = $matches[1][$i];
            $requireScore = sprintf("%.1f",floatval($matches[6][$i]));
            $score = sprintf("%.1f",floatval($matches[7][$i]));
            if($requireScore == 0.0){//16级特判
                $requireScore = $scoreArr[$i];
            }
            $item = ['name' => $name, 'requireScore' => $requireScore, 'score' => $score];
            $scoreList [] = $item;

        }
        return $scoreList;
    }
    /**
     * 解析信息门户处的每日提醒信息 从官方的json中取部分内容
     * 官方：[{"needUpdate":false,"userIdentification":null,"userName":"","description":"无提醒信息","occurException":true,"authIntegration":true,"contentUrlClass":"","hiddenCondition":0,"title":"【财务收费】","attchmentId":"","id":"41","lookLinkOnClick":0,"sequenceNumber":"3","invokeMethod":"","password":"","needAuthIntegration":0,"sourceUrl":"select num from v_dbsy_cw where userid=?","noDataNeedShow":1,"urlClass":"","needShowPic":0,"linkUrl":"http://cw.gdufe.edu.cn/KfWeb/LoginInterface.aspx","sourceType":"db"},{"needUpdate":false,"userIdentification":null,"userName":"","description":"您截止到昨天的余额是<span>199.32<\/span>元。","occurException":false,"authIntegration":true,"contentUrlClass":"","hiddenCondition":0,"title":"【一卡通】","attchmentId":"","id":"101","lookLinkOnClick":0,"sequenceNumber":"7","invokeMethod":"","password":"","needAuthIntegration":0,"sourceUrl":"select ye from V_YKT_KH where sfrzh=?","noDataNeedShow":1,"urlClass":"","needShowPic":0,"linkUrl":"http://cardinfo.gdufe.edu.cn/gdcjportalHome.action","sourceType":"db"},{"needUpdate":false,"userIdentification":null,"userName":"","description":"无提醒信息","occurException":true,"authIntegration":true,"contentUrlClass":"","hiddenCondition":0,"title":"【学工系统】","attchmentId":"","id":"21","lookLinkOnClick":0,"sequenceNumber":"10","invokeMethod":"","password":"","needAuthIntegration":0,"sourceUrl":"select num from v_dbsy_xg where userid=?","noDataNeedShow":1,"urlClass":"","needShowPic":0,"linkUrl":"http://xg.gdufe.edu.cn/epstar","sourceType":"db"},{"needUpdate":false,"userIdentification":null,"userName":"","description":"您共借阅<font color=\"red\"><span>0<\/span><\/font>本书。","occurException":false,"authIntegration":true,"contentUrlClass":"","hiddenCondition":0,"title":"【图书馆】","attchmentId":"","id":"121","lookLinkOnClick":0,"sequenceNumber":"11","invokeMethod":"","password":"","needAuthIntegration":0,"sourceUrl":"select count(*) num from v_tsg_jy  where sfrzh=?","noDataNeedShow":1,"urlClass":"","needShowPic":0,"linkUrl":"http://opac.library.gdufe.edu.cn/reader/hwthau.php","sourceType":"db"}]
     * 该函数返回：[{"id":"41","sequenceNumber":"3","title":"【财务收费】","description":"无提醒信息","linkUrl":"http://cw.gdufe.edu.cn/KfWeb/LoginInterface.aspx"},{"id":"101","sequenceNumber":"7","title":"【一卡通】","description":"您截止到昨天的余额是<span>167.12</span>元。","linkUrl":"http://cardinfo.gdufe.edu.cn/gdcjportalHome.action"},{"id":"21","sequenceNumber":"10","title":"【学工系统】","description":"无提醒信息","linkUrl":"http://xg.gdufe.edu.cn/epstar"},{"id":"121","sequenceNumber":"11","title":"【图书馆】","description":"您共借阅<font color=\"red\"><span>0</span></font>本书。","linkUrl":"http://opac.library.gdufe.edu.cn/reader/hwthau.php"}]
     * @param $html
     * @return array
     */
    public function parseInfoTips($html)
    {
        if (empty($html)) return [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $obj = json_decode($html,true);
        $count_json = count($obj);
        $tips = array();
        for ($i = 0; $i < $count_json; $i++){
            $id = $obj[$i]['id'];
            $sequenceNumber = $obj[$i]['sequenceNumber'];
            $title = $obj[$i]['title'];
            $description = $obj[$i]['description'];
            $linkUrl = $obj[$i]['linkUrl'];
            $item = compact(
                'id','sequenceNumber','title','description','linkUrl'
            );
            $tips []= $item;
        }
        return $tips;
    }

}