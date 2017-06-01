<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Faker\Provider\Base;
use Yii;
use yii\web\Controller;

class JwController  extends BaseController
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
     * @apiVersion 1.0.3
     * @apiName get-grade
     * @apiGroup Jw
     * @apiDescription 获取成绩
     *
     * @apiParam {String} sno       学号
     * @apiParam {String} pwd       教务系统密码
     * @apiParam {String} stu_time  可选，学年学期，格式：2014-2015-2，默认返回整个大学（全部已修学期）
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      成绩单列表
     * @apiSuccess {String}     data.name       课程名
     * @apiSuccess {String}     data.time       学年学期，格式：2014-2015-2
     * @apiSuccess {int}        data.score      总分，优良中差对应返回98，85，75，65
     * @apiSuccess {int}        data.credit     学分
     * @apiSuccess {int}        data.classCode     课程编号
     * @apiSuccess {int}        data.dailyScore     平时成绩
     * @apiSuccess {int}        data.expScore       实验成绩
     * @apiSuccess {int}        data.paperScore     期末卷面成绩
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"time":"2014-2015-1","name":"离散数学","score":91,"credit":4,"classCode":"110094","dailyScore":99,"expScore":0,"paperScore":87},{"time":"2014-2015-1","name":"数据结构","score":95,"credit":4,"classCode":"110104","dailyScore":94,"expScore":0,"paperScore":95}]}
     * @apiErrorExample  {json} 异常返回
     * {"code":3001,"msg":"学号或密码错误","data":[]}
     */
    public function actionGetGrade()
    {
        if(isset($this->req['stu_time'])){
            $this->data['stu_time'] = $this->req['stu_time'];
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
     * @apiParam {String} sno       学号
     * @apiParam {String} pwd       教务系统密码
     * @apiParam {String} stu_time  可选，学年学期，格式：2014-2015-2，默认返回当前学期
     * @apiParam {int} split        可选，是否拆分连堂的课程，默认为0表示不拆分（连堂则合并成一个课程）
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
     * @apiParam {String} sno 学号
     * @apiParam {String} pwd 教务系统密码
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


}
