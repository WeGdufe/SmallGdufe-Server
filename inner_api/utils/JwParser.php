<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/3
 */
use yii\web\Response;
use yii;
use PHPHtmlParser\Dom;

trait JwParser
{
    public function parseGrade($html)
    {
        if(empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html,[]);
        $contents = $dom->find('table[id=dataList] tr');
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;      //标题头
            $name = $content->find('td', 3)->innerHtml;
            $score = $content->find('td a')->innerHtml;
            $credit = $content->find('td', 5)->innerHtml;
            $item = compact(
                'name', 'score', 'credit'
                // 'xueqi'
            );
            $scoreList [] = $item;
        }
        unset($dom);
        return $scoreList;
    }

    /**
     * 解析教务处网获取的个人基本信息
     * @param $html
     * @return array|null
     */
    public function parseBasicInfo($html)
    {
        if (empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('table[id=xjkpTable] tr');
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;

        $department = explode('：',$contents[2]->find('td', 0)->innerHtml)[1];
        $major = explode('：',$contents[2]->find('td',1)->innerHtml)[1];
        $class = explode('：',$contents[2]->find('td',3)->innerHtml)[1];

        $name = str_replace('&nbsp;','',$contents[3]->find('td', 1)->innerHtml);
        $sex = str_replace('&nbsp;','',$contents[3]->find('td',3)->innerHtml);
        //姓名拼音
        $namePy = str_replace('&nbsp;','',$contents[3]->find('td',5)->innerHtml);

        $birthday = str_replace('&nbsp;','',$contents[4]->find('td', 1)->innerHtml);
        $party = str_replace('&nbsp;','',$contents[5]->find('td',3)->innerHtml);
        $nation = str_replace('&nbsp;','',$contents[7]->find('td',3)->innerHtml);
        $education = str_replace('&nbsp;','',$contents[8]->find('td',3)->innerHtml);

        //身份证
        // $idNum = str_replace('&nbsp;','',$contents[43]->find('td',3)->innerHtml);

        $item = compact(
            'department', 'major', 'class',
            'name', 'sex', 'namePy',
            'birthday','party','nation',
            'education'
        );
        $scoreList [] = $item;
        return $scoreList;
    }

    /**
     * 返回合并连堂后的课表item，多个小节连堂的情况合并成一个item
     * @param $html
     * @return array|null
     */
    public function parseScheduleMergeNext($html){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $scheduleArr = $this->parseSchedule2Array($html);
        return $this->mergeScheduleNext($scheduleArr);
    }

    /**
     * 返回课表item，如果有多个小节连堂的情况返回的是分开多个item的，这个是原生处理
     * @param $html
     * @return array|null
     */
    public function parseSchedule($html){
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->parseSchedule2Array($html);
    }

    /**
     * 最核心的解析原生html文本，转成数组，
     * 是否合并多个小节相同的情况由调用者负责
     * @param $html
     * @return array|null
     */
    private function parseSchedule2Array($html)
    {
        if(empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html,[]);
        $contents = $dom->find('table[id=kbtable] div');
        unset($dom);

        $scoreList = array();
        $section = -2;  //这个数字+正则匹配的第二个数字 = 实际第几节，该数字代表有多少个两小节
        $oldSectionId = "根据id和id里的数字判断第几节";
        foreach ($contents as $index => $content) {

            if( 'kbcontent' == $content->getAttribute('class')) {

                /** ↓ 解析课程名和课程起始节数 */
                //="A0510F68580D494A9E7F609059E8DDDF-2-2" style="display: none;" class="kbcontent">计算机网络<br />
                // <div id="72347173204F4B62B72C84B5867495E4-6-2" style="display: none;" class="kbcontent">&nbsp;</div>
                /** 如上面的2-2代表星期二，该单元格第二小节 但并不知道实际是第几小节，
                 * 故需根据id字符串去判断是第几个行，加上这个第二小节去算
                 */
                $pattern = "/(\\w{32})-(\\d)-(\\d).+?>(.+?)</"; //&nbsp;也要匹配到 为了计换节数
                preg_match($pattern,$content,$timeRes);
                if($timeRes[1] != $oldSectionId){
                    //换节了，之前是第一二节，现在是第三四节
                    $section +=2;
                }
                $oldSectionId = $timeRes[1];
                $endSec = $section + $timeRes[3];
                $startSec = $endSec - 1 ;   //两小节为一单位，3个小节的也官方认为是4个小节
                $dayInWeek = intval($timeRes[2]);
                $name = $timeRes[4];

                /** 解析老师、周等常规数据，去掉了&nbsp;的情况，
                 * 这部分代码和上面代码不能交换顺序，
                 * 否则在5-6节(数字是比如)全空的情况下startSec会少2 */
                $teacherObj = $content->find('font',0);
                if(isset($teacherObj)) {
                    $teacher = $teacherObj->innerHtml;
                }else{
                    //&nbsp;的情况 整个不要了，但$section还是有累加到的
                    continue;
                }
                $period = $content->find('font',1)->innerHtml;
                $location = $content->find('font',2)->innerHtml;

                $item = compact(
                    'name', 'teacher', 'period',
                    'location','dayInWeek',
                    'startSec','endSec'
                );
                $scoreList [] = $item;
            }
        }
        return $scoreList;
    }

    /**
     * 合并课程表的连堂情况，同一天里同课程且同地点且连续节且周安排也符合 则认为是连堂，故单双周不认为是连堂
     * 暴力两个for，检测当前$indexUp跟未来的$indexDown无连堂则添加当前$indexUp，有连堂则修改$indexUp后添加，且把$indexDown加入黑名单
     * 可处理连若干堂，包括全天连堂课
     * @param $scheduleArr
     * @return array
     */
    private function mergeScheduleNext($scheduleArr){
        $mergedArr = array();     //结果数组
        $blackIndex = array();    //黑名单数组，数组里的下标跟以前的可合并，则不添加到结果里
        foreach ($scheduleArr as $indexUp => $itemUp) {
            $newItem = $itemUp;

            foreach ($scheduleArr as $indexDown => $itemDown) {
                // if($indexUp > $indexDown) continue; //注释原因：连续多堂的情况必须跑n*n
                if(
                    $indexUp != $indexDown
                    && $newItem['dayInWeek'] == $itemDown['dayInWeek']
                    && $newItem['endSec'] + 1 == $itemDown['startSec']
                    && $newItem['location'] == $itemDown['location']
                    && $newItem['name'] == $itemDown['name']
                    && $newItem['period'] == $itemDown['period']
                ){
                    $newItem['startSec'] = min($newItem['startSec'],$itemDown['startSec']);
                    $newItem['endSec'] = max($newItem['endSec'],$itemDown['endSec']);
                    $blackIndex [] = $indexDown;
                }
            }
            if( in_array($indexUp,$blackIndex) ){
                continue;
            }
            $mergedArr [] = $newItem;        //外层循环里，只加一次
        }
        return $mergedArr;
    }

    /**
     * 同上，但只处理四节连堂，不处理6节，8节。。。
     * @param $scheduleArr
     * @return array
     */
    private function mergeScheduleNext2($scheduleArr){
        $mergedArr = array();     //结果数组
        $blackIndex = array();    //黑名单数组，数组里的下标跟以前的可合并，则不添加到结果里
        foreach ($scheduleArr as $indexUp => $itemUp) {
            $newItem = $itemUp;

            foreach ($scheduleArr as $indexDown => $itemDown) {
                if($indexUp > $indexDown) continue; //n*n矩阵的左上角，避免重复处理

                if(
                    $indexUp != $indexDown
                    && $itemUp['dayInWeek'] == $itemDown['dayInWeek']
                    && $itemUp['endSec'] + 1 == $itemDown['startSec']
                    && $itemUp['location'] == $itemDown['location']
                    && $itemUp['name'] == $itemDown['name']
                    && $itemUp['period'] == $itemDown['period']
                ){
                    //min自己是避免越改越小，当然因为网站的返回是按1-2,3-4小节这种时间顺序的所以不加也没问题
                    $newItem['startSec'] = min($newItem['startSec'],$itemUp['startSec'],$itemDown['startSec']);
                    $newItem['endSec'] = max($newItem['endSec'],$itemUp['endSec'],$itemDown['endSec']);

                    $blackIndex [] = $indexDown;
                    break;
                }
            }
            if( in_array($indexUp,$blackIndex) ){
                continue;
            }
            $mergedArr [] = $newItem;        //外层循环里，只加一次
        }
        return $mergedArr;
    }
}