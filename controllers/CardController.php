<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

//校园卡
class CardController extends AuthBaseController
{

    /**
     * @api {post} card/current-cash 获取校园卡余额
     * @apiVersion 1.0.0
     * @apiName current-cash
     * @apiGroup Card
     *
     * @apiDescription 获取校园卡卡号和余额
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}   data      校园卡状况
     * @apiSuccess {String}     data.cardNum        校园卡卡号
     * @apiSuccess {String}     data.cash           当前余额
     * @apiSuccess {String}     data.cardState      卡状态
     * @apiSuccess {String}     data.checkState     检查状态
     * @apiSuccess {String}     data.lossState      挂失状态
     * @apiSuccess {String}     data.freezeState    冻结状态
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{"cash":"152.62","cardState":"正常","checkState":"正常","lossState":"正常","freezeState":"正常"}}
     */
    public function actionCurrentCash()
    {
        return Yii::$app->runAction('api/card/current-cash', $this->data);
    }


    /**
     * @api {post} card/consume-today 获取校园卡当日交易记录
     * @apiVersion 1.0.0
     * @apiName consume-today
     * @apiGroup Card
     *
     * @apiDescription 获取校园卡当日交易记录，含充值和消费等（0点跨日）
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     * @apiParam {String} cardNum 校园卡卡号（可从current-cash获取）
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      交易记录
     * @apiSuccess {String}     data.time   交易时间
     * @apiSuccess {String}     data.shop   交易商户
     * @apiSuccess {String}     data.change 交易额
     * @apiSuccess {String}     data.cash   交易后的余额
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     * @apiError 4000 校园卡卡号为空 / 缺少<code>cardNum</code>参数
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"time":"2017/03/10 17:34:34","shop":"广州校区第二食堂","change":"-11.60","cash":"141.02"},{"time":"2017/03/10 11:34:54","shop":"广州校区二饭堂合作方","change":"-7.00","cash":"152.62"}]}
     */
    public function actionConsumeToday()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if(isset($req['cardNum'])){ //必备参数，若缺则api/去检测返回
            $this->data['cardNum'] = $req['cardNum'];
        }
        return Yii::$app->runAction('api/card/consume-today', $this->data);
    }
    /**
     * @api {post} card/get-electric 宿舍电控查询
     * @apiVersion 1.0.5
     * @apiName get-electric
     * @apiGroup Card
     *
     * @apiDescription 查用电、购电情况，支持本部23，26，27，29，30，32栋
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     * @apiParam {String} building  宿舍楼号
     * @apiParam {String} room      宿舍房间号
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      交易记录
     * @apiSuccess {String}     data.electric 电量剩余度数
     * @apiSuccess {String}     data.money 剩余电费
     * @apiSuccess {String}     data.time 时间
     *
     * @apiError 6300 房间号错误或者最近一周系统无记录
     * @apiError 6301 楼号错误或者暂不支持
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"electric":"5.0","money":"3.24","time":"2017-09-02 10:00:00"},{"electric":"37.51","money":"24.27","time":"2017-09-03 10:00:00"},{"electric":"29.92","money":"19.36","time":"2017-09-04 10:00:01"},{"electric":"21.2","money":"13.72","time":"2017-09-05 10:00:00"},{"electric":"10.91","money":"7.06","time":"2017-09-06 10:00:01"}]}
     */
    public function actionGetElectric() {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        $this->data['building'] = 0;
        $this->data['room'] = 0;
        if(isset($req['building'])){
            $this->data['building'] = $req['building'];
        }
        if(isset($req['room'])){
            $this->data['room'] = $req['room'];
        }
        return Yii::$app->runAction('api/card/get-electric', $this->data);
    }

    public function actionTest()
    {
        return Yii::$app->runAction('api/card/test');
    }
}
