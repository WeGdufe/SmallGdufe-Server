<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Faker\Provider\Base;
use Yii;
use yii\web\Controller;

class InfoController extends BaseController
{

    public function actionTest()
    {
        // $stu_time = Yii::$app->request->get('stu_time');
        // $this->data['stu_time'] = '2014-2015-2';
        return Yii::$app->runAction('api/info/test', $this->data);
    }

    /**
     * @api {post} info/few-sztz 获取素拓信息
     * @apiVersion 1.0.0
     * @apiName few-sztz
     * @apiGroup Info
     *
     * @apiDescription 获取素拓学分信息
     *
     * @apiParam {String} sno 学号
     * @apiParam {String} pwd 信息门户密码
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      各模块素拓学分情况
     * @apiSuccess {String}     data.name           素拓模块名
     * @apiSuccess {String}     data.requireScore   所需最少学分
     * @apiSuccess {String}     data.score          已修学分
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"name":"身心素质","requireScore":"1.5","score":"4.4"},{"name":"文化艺术素质","requireScore":"1.5","score":"6.8"},{"name":"技能素质","requireScore":"1.5","score":"4.9"},{"name":"思想品德素质","requireScore":"2.0","score":"9.2"},{"name":"创新创业素质","requireScore":"2.5","score":"8.0"},{"name":"任选","requireScore":"1.0","score":"0.0"}]}
     */
    public function actionFewSztz()
    {
        return Yii::$app->runAction('api/info/few-sztz', $this->data);
    }

    /**
     * @api {post} info/info-tips 获取每日提醒信息
     * @apiVersion 1.0.0
     * @apiName info-tips
     * @apiGroup Info
     *
     * @apiDescription 信息门户右上角那个提醒信息，目前未被使用，返回部分的官方信息，官方完整里有sql语句
     *
     * @apiParam {String} sno 学号
     * @apiParam {String} pwd 信息门户密码
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      从官方处截取部分的提醒信息
     * @apiSuccess {String}     data.id               id，不明用途
     * @apiSuccess {String}     data.sequenceNumber   序列号，不明用途
     * @apiSuccess {String}     data.title            标题，含模块信息
     * @apiSuccess {String}     data.description      描述
     * @apiSuccess {String}     data.linkUrl          对应的跳转的链接
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"id":"41","sequenceNumber":"3","title":"【财务收费】","description":"无提醒信息","linkUrl":"http://cw.gdufe.edu.cn/KfWeb/LoginInterface.aspx"},{"id":"101","sequenceNumber":"7","title":"【一卡通】","description":"您截止到昨天的余额是<span>167.12</span>元。","linkUrl":"http://cardinfo.gdufe.edu.cn/gdcjportalHome.action"},{"id":"21","sequenceNumber":"10","title":"【学工系统】","description":"无提醒信息","linkUrl":"http://xg.gdufe.edu.cn/epstar"},{"id":"121","sequenceNumber":"11","title":"【图书馆】","description":"您共借阅<font color=\"red\"><span>0</span></font>本书。","linkUrl":"http://opac.library.gdufe.edu.cn/reader/hwthau.php"}]}
     */
    public function actionInfoTips()
    {
        return Yii::$app->runAction('api/info/info-tips', $this->data);
    }

}
