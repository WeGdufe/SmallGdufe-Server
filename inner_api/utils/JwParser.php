<?php
namespace app\inner_api\utils;

/**
 * User: xiaoguang
 * Date: 2017/2/3
 */
use stdClass;
use yii\web\Response;
use yii;
use PHPHtmlParser\Dom;

trait JwParser
{
    public function parseGrade($html)
    {
        if (empty($html)) return [];
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('table[id=dataList] tr');
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            if ($index == 0) continue;      //标题头
            $time = $content->find('td', 1)->innerHtml;
            $name = $content->find('td', 3)->innerHtml;
            $score = $content->find('td a')->innerHtml;
            switch ($score) {
                case '优':
                    $score = 95;
                    break;
                case '良':
                    $score = 85;
                    break;
                case '中':
                    $score = 75;
                    break;
                case '差':
                    $score = 65;
                    break;
                default:
                    $score = intval($score);
                    break;
            }
            $credit = intval($content->find('td', 5)->innerHtml);
            $item = compact(
                'time','name', 'score', 'credit'
            );
            $scoreList [] = $item;
        }
        unset($dom);
        return $scoreList;
    }

    /**
     * 解析教务处网获取的个人基本信息
     * @param $html
     * @return object
     */
    public function parseBasicInfo($html)
    {
        if (empty($html)) return new stdClass;
        $dom = new Dom;
        $dom->loadStr($html, []);
        $contents = $dom->find('table[id=xjkpTable] tr');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $department = explode('：', $contents[2]->find('td', 0)->innerHtml)[1];  //学院
        $major = explode('：', $contents[2]->find('td', 1)->innerHtml)[1];       //专业
        $classroom = explode('：', $contents[2]->find('td', 3)->innerHtml)[1];   //班级

        $name = $this->trimNbsp($contents[3]->find('td', 1)->innerHtml);
        $sex = $this->trimNbsp($contents[3]->find('td', 3)->innerHtml);         //性别
        $namePy = $this->trimNbsp($contents[3]->find('td', 5)->innerHtml);      //姓名拼音
        $birthday = $this->trimNbsp($contents[4]->find('td', 1)->innerHtml);    //生日
        $party = $this->trimNbsp($contents[5]->find('td', 3)->innerHtml);       //党员/群众
        $nation = $this->trimNbsp($contents[7]->find('td', 3)->innerHtml);      //民族
        $education = $this->trimNbsp($contents[8]->find('td', 3)->innerHtml);   //本科/研究生

        //身份证
        // $idNum = str_replace('&nbsp;','',$contents[43]->find('td',3)->innerHtml);

        $item = compact(
            'department', 'major', 'classroom',
            'name', 'sex', 'namePy',
            'birthday', 'party', 'nation',
            'education'
        );
        return $item;
    }
    //去掉&nbsp;和首尾空格
    private function trimNbsp($str){
        return str_replace('&nbsp;', '',trim($str));
    }

    /**
     * 返回合并连堂后的课表item，多个小节连堂的情况合并成一个item，优化连堂
     * @param $html
     * @return array|null
     */
    public function parseScheduleMergeNext($html)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $scheduleArr = $this->parseSchedule2Array($html);
        $mergedArr = $this->mergeScheduleNext($scheduleArr);
        return $this->mergeScheduleDifferentWeek($mergedArr);
    }

    /**
     * 返回课表item，如果有多个小节连堂的情况返回的是分开多个item的，这个是原生处理
     * @param $html
     * @return array|null
     */
    public function parseSchedule($html)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $scheduleArr = $this->parseSchedule2Array($html);
        return $this->mergeScheduleDifferentWeek($scheduleArr);
    }

    /**
     * 最核心的解析原生html文本，转成数组，连堂和形势政策这种不同周的情况都是分开成多个item
     * 是否合并连堂和形势政策这种跳周的 都由调用者负责
     * 因页面的小节信息处代码不规范，有</font>却没有<font>故只能手写正则
     * @param $html
     * @return array
     */
    private function parseSchedule2Array($html)
    {
        if (empty($html)) return [];

        $pattern = '.+?kbcontent1.+?';
        $pattern .= '<div id="\w{32}(.+?)kbcontent"(.+?)div>';//查找有课程信息的div
        preg_match_all('/' . $pattern . '/s', $html, $divMat);

        $scoreList = [];
        foreach ($divMat[2] as $index => $content) {

            // 求星期几
            $pattern = '#-(\d)-\d#';
            $matchContent = $divMat[1][$index];
            preg_match($pattern, $matchContent, $timeRes);
            $dayInWeek = intval($timeRes[1]);

            //其他课程信息
            $matchContent = $content;
            $pattern = '>(.+?)<br\/>';
            for ($i = 0; $i < 3; $i++) {
                $pattern .= '.+?\'>(.+?)<\/font>';
            }
            $pattern .= '.+?\[(.+?)\].+?<br\/>';
            preg_match_all('/' . $pattern . '/', $matchContent, $matches);

            $resCnt = count($matches[0]);
            if($resCnt == 0){   //忽略空数据情况（就是那个单元格没有课程的情况）
                continue;
            }
            //不管是一个形势政策还是多个不同周的 都循环解决
            for($ith = 0; $ith < $resCnt; $ith++){

                $name = $matches[1][$ith];
                $teacher = $matches[2][$ith];
                $period = $matches[3][$ith];        //周
                $location = $matches[4][$ith];

                $expArr = explode('-', $matches[5][$ith]);
                $startSec = intval($expArr[0]);
                if (1 == count($expArr)) {      //[11]这种单节
                    $endSec = intval($startSec);
                } else {                        //[3-4]双节
                    $endSec = intval($expArr[1]);
                }
                $item = compact(
                    'name','teacher'
                    ,'period','location','dayInWeek'
                    ,'startSec', 'endSec'
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
    private function mergeScheduleNext($scheduleArr)
    {
        $mergedArr = array();     //结果数组
        $blackIndex = array();    //黑名单数组，数组里的下标跟以前的可合并，则不添加到结果里
        foreach ($scheduleArr as $indexUp => $itemUp) {
            $newItem = $itemUp;
            foreach ($scheduleArr as $indexDown => $itemDown) {
                // if($indexUp > $indexDown) continue; //注释原因：连续多堂的情况必须跑n*n
                if (
                    $indexUp != $indexDown
                    && $newItem['dayInWeek'] == $itemDown['dayInWeek']
                    && $newItem['endSec'] + 1 == $itemDown['startSec']
                    && $newItem['location'] == $itemDown['location']
                    && $newItem['name'] == $itemDown['name']
                    && $newItem['period'] == $itemDown['period']
                ) {
                    $newItem['startSec'] = min($newItem['startSec'], $itemDown['startSec']);
                    $newItem['endSec'] = max($newItem['endSec'], $itemDown['endSec']);
                    $blackIndex [] = $indexDown;
                }
            }
            if (in_array($indexUp, $blackIndex)) {
                continue;
            }
            $mergedArr [] = $newItem;        //外层循环里，只加一次
        }
        return $mergedArr;
    }

    /**
     * 合并形势政策这种除了周数不同外其他都相同的课程
     * @param $scheduleArr
     * @return array
     */
    private function mergeScheduleDifferentWeek($scheduleArr)
    {
        $mergedArr = array();     //结果数组
        $blackIndex = array();    //黑名单数组，数组里的下标跟以前的可合并，则不添加到结果里
        foreach ($scheduleArr as $indexUp => $itemUp) {
            $newItem = $itemUp;
            foreach ($scheduleArr as $indexDown => $itemDown) {
                if (
                    $indexUp != $indexDown
                    && $newItem['dayInWeek'] == $itemDown['dayInWeek']
                    && $newItem['endSec'] == $itemDown['endSec']
                    && $newItem['startSec'] == $itemDown['startSec']
                    && $newItem['location'] == $itemDown['location']
                    && $newItem['name'] == $itemDown['name']
                    && $newItem['period'] != $itemDown['period']
                ) {
                    //先简单合并成 11(单周),15(单周),7(单周)  这样
                    $newItem['period'] = $newItem['period'] . "," . $itemDown['period'];
                    $blackIndex [] = $indexDown;
                }
            }
            if (in_array($indexUp, $blackIndex)) {
                continue;
            }

            //如需更改结果格式为 7,11,15(单周) 这种则在这写正则
            $mergedArr [] = $newItem;        //外层循环里，只加一次
        }
        return $mergedArr;
    }

    /**
     * 同上，但只处理四节连堂，不处理6节，8节。。。
     * @param $scheduleArr
     * @return array
     */
    private function mergeScheduleNext2($scheduleArr)
    {
        $mergedArr = array();     //结果数组
        $blackIndex = array();    //黑名单数组，数组里的下标跟以前的可合并，则不添加到结果里
        foreach ($scheduleArr as $indexUp => $itemUp) {
            $newItem = $itemUp;

            foreach ($scheduleArr as $indexDown => $itemDown) {
                if ($indexUp > $indexDown) continue; //n*n矩阵的左上角，避免重复处理

                if (
                    $indexUp != $indexDown
                    && $itemUp['dayInWeek'] == $itemDown['dayInWeek']
                    && $itemUp['endSec'] + 1 == $itemDown['startSec']
                    && $itemUp['location'] == $itemDown['location']
                    && $itemUp['name'] == $itemDown['name']
                    && $itemUp['period'] == $itemDown['period']
                ) {
                    //min自己是避免越改越小，当然因为网站的返回是按1-2,3-4小节这种时间顺序的所以不加也没问题
                    $newItem['startSec'] = min($newItem['startSec'], $itemUp['startSec'], $itemDown['startSec']);
                    $newItem['endSec'] = max($newItem['endSec'], $itemUp['endSec'], $itemDown['endSec']);

                    $blackIndex [] = $indexDown;
                    break;
                }
            }
            if (in_array($indexUp, $blackIndex)) {
                continue;
            }
            $mergedArr [] = $newItem;        //外层循环里，只加一次
        }
        return $mergedArr;
    }

}


