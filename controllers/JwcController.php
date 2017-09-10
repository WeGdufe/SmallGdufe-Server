<?php

namespace app\controllers;

use yii\web\Controller;
use Yii;

//不需要登陆，故不继承BaseController
class JwcController extends Controller
{
    private $req;
    private $data;
    /**
     * @api {post} jwc/get-xiaoli 获取校历、上课时间表
     * @apiVersion 1.0.0
     * @apiName get-xiaoli
     * @apiGroup Jwc
     *
     * @apiDescription 从教务处获取校历、上课时间表图片地址url
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}   data      url地址集
     * @apiSuccess {String}     data.timeTable      课时间表图片地址
     * @apiSuccess {String}     data.xiaoLi         校历图片地址
     *
     * @apiSuccessExample {json} 正常返回
     *  {"code":0,"msg":"","data":{"timeTable":"http://jwc.gdufe.edu.cn/attach/2016/10/20/769667.jpg","xiaoLi":"http://jwc.gdufe.edu.cn/attach/2016/10/20/769666.jpg"}}
     */
    public function actionGetXiaoli()
    {
        return Yii::$app->runAction('api/jwc/get-xiaoli');
    }

    /**
     * @api {post} jwc/get-cet 获取四六级成绩
     * @apiVersion 1.0.0
     * @apiName get-cet
     * @apiGroup Jwc
     *
     * @apiDescription 获取四六级成绩，只能查询最近一次的
     *
     * @apiParam {String} zkzh  准考证号
     * @apiParam {String} xm    姓名
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}   data      四六级
     * @apiSuccess {String}     data.name           姓名
     * @apiSuccess {String}     data.school         学校
     * @apiSuccess {String}     data.level          英语四级/英语六级
     * @apiSuccess {String}     data.cetId          准考证号
     * @apiSuccess {String}     data.score          总分数，若作弊或小于220则全部成绩显示0分
     * @apiSuccess {String}     data.listenScore    听力分数
     * @apiSuccess {String}     data.readScore      阅读分数
     * @apiSuccess {String}     data.writeScore     写作分数
     *
     * @apiError 5300 手抖输错准考证号或者姓名了吧
     * @apiError 5301 考号或者名字为空
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{"name":"某某某","school":"广东财经大学","level":"英语六级","cetId":"440100162200601","score":"0","listenScore":"0","readScore":"0","writeScore":"0"}}
     */
    public function actionGetCet()
    {
        $this->req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (isset($this->req['zkzh']) && isset($this->req['xm'])) {
            $this->data['zkzh'] = $this->req['zkzh'];
            $this->data['xm'] = $this->req['xm'];
        }
        return Yii::$app->runAction('api/jwc/get-cet', $this->data);
    }
    public function actionTest()
    {
        return Yii::$app->runAction('api/jwc/test', $this->data);
    }

}
