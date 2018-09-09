<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Faker\Provider\Base;
use Yii;
use yii\web\Controller;

class JwController  extends AuthBaseController
{

    public function actionTest()
    {
        $this->data['stu_time'] = '2014-2015-2';
        if(isset($this->req['split'])){
            $this->data['split'] = intval($this->req['split']);
        }else{
            $this->data['split'] = 0;
        }
        return Yii::$app->runAction('api/jw/test', $this->data);
    }

    /**
     * @api {post} jw/get-grade 获取成绩
     * @apiVersion 1.0.0
     * @apiName get-grade
     * @apiGroup Jw
     * @apiDescription 获取成绩，显示全部成绩
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     * @apiParam {String} stu_time  可选，学年学期，格式：2014-2015-2 和 2014-2015（返回学年成绩），为空则默认返回整个大学（全部已修学期）
     * @apiParam {int} minor        可选，查询辅修成绩为1，查主修为0，默认为0查主修
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      成绩单列表
     * @apiSuccess {String}     data.name       课程名
     * @apiSuccess {String}     data.time       学年学期，格式：2014-2015-2
     * @apiSuccess {int}        data.score      总分，优良中差对应返回98，85，75，65
     * @apiSuccess {float}     data.credit     学分，有0.5学分的情况，整数学分则为纯整数
     * @apiSuccess {int}        data.classCode     课程编号
     * @apiSuccess {int}        data.dailyScore     平时成绩
     * @apiSuccess {int}        data.expScore       实验成绩
     * @apiSuccess {int}        data.paperScore     期末卷面成绩
     * @apiSuccess {String}        data.examType     考试类型，正常考试、补考一、补考二等
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     * @apiError 3100 没评教，去成绩打印机处查询吧
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"time":"2014-2015-2","name":"面向对象程序设计","score":74,"credit":4,"classCode":110154,"dailyScore":0,"expScore":0,"paperScore":0,"examType":"补考一"},{"time":"2016-2017-2","name":"就业指导","score":94,"credit":0.5,"classCode":400025,"dailyScore":92,"expScore":0,"paperScore":96,"examType":"正常考试"}]}
     * @apiErrorExample  {json} 异常返回
     * {"code":3001,"msg":"学号或密码错误","data":[]}
     */
    public function actionGetGrade()
    {
        if(isset($this->req['stu_time'])){
            $this->data['stu_time'] = $this->req['stu_time'];
        }
        if(isset($this->req['minor'])){
            $this->data['minor'] = $this->req['minor'];
        }

        return Yii::$app->runAction('api/jw/get-grade', $this->data);
    }

    /**
     * @api {post} jw/get-schedule 获取课程表
     * @apiVersion 1.0.0
     * @apiName get-schedule
     * @apiGroup Jw
     *
     * @apiDescription 获取课程表
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     * @apiParam {String} stu_time  可选，学年学期，格式：2014-2015-2，默认返回当前学期
     * @apiParam {int} split        可选，是否拆分连堂的课程，默认为0表示不拆分（连堂则合并成一个课程）
     * @apiParam {int} week         可选，按周查看，数字1-16，格式：8，默认返回全部
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      课程表
     * @apiSuccess {String}     data.name       课程名
     * @apiSuccess {String}     data.teacher    上课老师，含职位
     * @apiSuccess {String}     data.period     上课周数，非连续周则逗号分隔
     * @apiSuccess {String}     data.location   上课教室
     * @apiSuccess {int}        data.dayInWeek  周几
     * @apiSuccess {int}        data.startSec   开始小节，最小为1
     * @apiSuccess {int}        data.endSec     结束小节，最大为12
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"name":"计算机系统结构","teacher":"陈某某副教授","period":"1-16(周)","location":"拓新楼(SS1)334","dayInWeek":1,"startSec":1,"endSec":2},{"name":"形势与政策","teacher":"黄某讲师（高校）","period":"11(单周),15(单周),7(单周)","location":"综合楼107","dayInWeek":2,"startSec":5,"endSec":8}]}
     * @apiErrorExample  {json} 异常返回
     * {"code":3000,"msg":"学号或者密码为空","data":[]}
     */
    public function actionGetSchedule()
    {
        //没有则查询当前学期
        if(isset($this->req['stu_time'])){
            $this->data['stu_time'] = $this->req['stu_time'];
        }
        if(isset($this->req['split'])){
            $this->data['split'] = intval($this->req['split']);
        }else{
            $this->data['split'] = 0;
        }
		//判断周参数是否为空
		if(isset($this->req['week'])){
            $this->data['week'] = $this->req['week'];
        }
        return Yii::$app->runAction('api/jw/get-schedule', $this->data);
    }

    /**
     * @api {post} jw/get-basic 获取个人基本信息
     * @apiVersion 1.0.0
     * @apiName get-basic
     * @apiGroup Jw
     *
     * @apiDescription 获取学生个人基本信息
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}   data      基本信息
     * @apiSuccess {String}     data.department         学院
     * @apiSuccess {String}     data.major              专业
     * @apiSuccess {String}     data.classroom          班级
     * @apiSuccess {String}     data.name               姓名
     * @apiSuccess {String}     data.sex                性别
     * @apiSuccess {String}     data.namePy             姓名拼音
     * @apiSuccess {String}     data.birthday           生日
     * @apiSuccess {String}     data.party              政治面貌
     * @apiSuccess {String}     data.nation             民族
     * @apiSuccess {String}     data.education          学历
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{"department":"信息学院","major":"计算机科学与技术","classroom":"2013计算机科学与技术2班","name":"韩裕光","sex":"男","namePy":"Han Yuguang","birthday":"19950124","party":"群众","nation":"汉族","education":"普通本科"}}
     * @apiErrorExample  {json} 异常返回
     * {"code":3000,"msg":"学号或者密码为空","data":{}}
     */
    public function actionGetBasic()
    {
        return Yii::$app->runAction('api/jw/get-basic', $this->data);
    }


    /**
     * @api {post} jw/get-exam-schedule 获取考试安排表
     * @apiVersion 1.0.6
     * @apiName get-exam-schedule
     * @apiGroup Jw
     *
     * @apiDescription 获取考试安排表
     *
     * @apiParam {String} appid 通用参数，类似的 sign、timestamp、token 等此处省略
     * @apiParam {String} stu_time  可选，学年学期，格式：2014-2015-2，默认返回当前学期
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}   data      基本信息
     * @apiSuccess {String}     data.name           课程
     * @apiSuccess {String}     data.time           时间
     * @apiSuccess {String}     data.xiaoqu         校区
     * @apiSuccess {String}     data.kaochang       考场
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * 暂无
     * @apiErrorExample  {json} 异常返回
     * {"code":3000,"msg":"学号或者密码为空","data":{}}
     */
    public function actionGetExamSchedule()
    {
        if(isset($this->req['stu_time'])){
            $this->data['stu_time'] = $this->req['stu_time'];
        }
        return Yii::$app->runAction('api/jw/get-exam-schedule', $this->data);
    }

    /**
     * @api {post} jw/get-empty-classroom 获取空课室
     * @apiVersion 1.0.6
     * @apiName get-empty-classroom
     * @apiGroup Jw
     *
     * @apiDescription 获取空课室
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     * @apiParam {String} stu_time  学年学期，格式：2014-2015-2
     * @apiParam {String} building  教学楼编号，["J1"=>"第一教学楼","B4"=>"经管实验楼","S1"=>"实验楼","Z1"=>"综合楼","B5"=>"第三教学楼","SJ3"=>"厚德楼","SJ1"=>"励学楼","SS1"=>"拓新楼","SJ2"=>"笃行楼"];
     * @apiParam {int} week  周数
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Array[]}   data      空课室列表
     * @apiSuccess {String}     data.item[0]           课室编号
     * @apiSuccess {Boolean}    data.item[1-42]        周一到周日每日六大节该课室是否为空
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[["106",true,false,false,false,false,false,false,false,false,false,true,true,false,false,false,false,false,true,false,false,false,false,false,true,true,false,true,false,true,true,true,true,true,true,true,true,true,true,true,true,true,true],["107",false,false,false,false,false,false,false,true,false,false,true,true,false,false,false,false,false,false,true,false,false,false,true,true,false,false,true,false,true,true,true,true,true,true,true,true,true,true,true,true,true,true],["108",false,false,false,true,true,true,false,false,false,false,true,true,false,false,false,true,false,false,true,true,false,true,true,true,false,false,false,false,true,true,true,true,true,true,true,true,true,true,true,true,true,true]]}
     * @apiErrorExample  {json} 异常返回
     * {"code":3000,"msg":"学号或者密码为空","data":{}}
     */
    public function actionGetEmptyClassroom()
    {
        if(isset($this->req['stu_time'])){
            $this->data['stu_time'] = $this->req['stu_time'];
        }
        if(isset($this->req['building'])){
            $this->data['building'] = $this->req['building'];
        }
        if(isset($this->req['week'])){
            $this->data['week'] = $this->req['week'];
        }
        return Yii::$app->runAction('api/jw/get-empty-classroom', $this->req);
    }


}
