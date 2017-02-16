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

    public function parseSchedule($html)
    {
        if(empty($html)) return null;
        $dom = new Dom;
        $dom->loadStr($html,[]);
        return ['kb'=>'待解析'];
        //TODO 解析课表
        $contents = $dom->find('table[id=kbtable] div');
        $scoreList = array();
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($contents as $index => $content) {
            $idVar = explode('-',$content->getAttribute('id'));
            // echo $idVar[1].' '.$idVar[2].' ';

            if( 'kbcontent' == $content->getAttribute('class') ) {
                // >(.+?)<.+?'>(.+?)<.+?'>(.+?)<.+?'>(.+?)<
                // preg_match('/>(.+?)<.+?\'>(.+?)<.+?\'>(.+?)<.+?\'>(.+?)</',$content,$res);
                // var_dump( $res );
                // echo $res[0];

                // $name = $res[1];
                // $teacher = $res[2];
                // $period = $res[3];
                // $locale = $res[4];
                // $item = ['name' => $name, 'teacher'=>$teacher,'period' => $period, 'locale' => $locale];


                // $content->ne
                // preg_match()
                // $dom->loadStr(
                // explode("<br />",$content->innerHtml)[1],[]);
                // $teacher = $dom->find('font',1)->innerHtml;
                // $period = $content->find('font',2)->innerHtml;
                // $locale = $content->find('font',3)->innerHtml;
                // $item = ['name' => $name, 'teacher'=>$teacher,'period' => $period, 'locale' => $locale];

                // print_r( $item );
                echo $content;
                echo  "\n\n";
            }
            // $scoreList [] = $item;
        }
        unset($dom);
        return $scoreList;
    }
}