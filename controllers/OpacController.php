<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

class OpacController extends AuthBaseController
{

    /**
     * @api {post} opac/search-book 获取书籍搜索结果
     * @apiVersion 1.0.0
     * @apiName search-book
     * @apiGroup Opac
     *
     * @apiDescription 返回书籍搜索结果，分页查询，每页最多20个，搜索方式为题目-前方一致，已过滤了serial为空（没馆藏）的情况
     *
     * @apiParam {String} bookName 书名
     * @apiParam {int} page 可选，分页查询的当前页数，默认为1
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      书籍搜索结果
     * @apiSuccess {String}     data.name       书名
     * @apiSuccess {String}     data.serial     序列号
     * @apiSuccess {int}        data.numAll     库藏总数量
     * @apiSuccess {int}        data.numCan     可借数量
     * @apiSuccess {String}     data.author     作者
     * @apiSuccess {String}     data.publisher  出版社
     * @apiSuccess {String}     data.macno      查看书本详细信息时用到的id
     *
     * @apiError 3300 书名为空
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"name":"解忧杂货店","serial":"I313.45/1093","numAll":4,"numCan":0,"author":"(日) 东野圭吾著","publisher":"南海出版公司 2014","macno":"0000422093"}]}
     */
    public function actionSearchBook()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['bookName'])) {
            $req['bookName'] = '';
        }
        if (!isset($req['page'])) {
            $req['page'] = 1;
        }
        $this->data['bookName'] = $req['bookName'];
        $this->data['page'] = $req['page'];
        return Yii::$app->runAction('api/opac/search-book', $this->data);
    }

    /**
     * @api {post} opac/top-book 获取热门书籍结果
     * @apiVersion 1.0.7
     * @apiName top-book
     * @apiGroup Opac
     *
     * @apiDescription 返回热门书籍结果
     *
     * @apiParam {String} type 类别:A-Z 默认为ALL
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      书籍搜索结果
     * @apiSuccess {String}     data.name       书名
     * @apiSuccess {String}     data.serial     序列号
     * @apiSuccess {String}     data.author     作者
     * @apiSuccess {String}     data.publisher  出版社
     * @apiSuccess {String}     data.macno      查看书本详细信息时用到的id
     *
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"name":"Kindle paperwhite电子书阅读器","serial":"G250.76/DZ1","author":"制造商：Amazon Fulfillment Service.Inc.","publisher":" 2013","macno":"76316e2b58723264747533683166503376674b5978513d3d"},{"name":"平凡的世界.2版","serial":"I247.57/768(2D)/1, I247.57/768(2D)/2,...","author":"路遥著","publisher":"北京十月文艺出版社 2012","macno":"3469564134426e79743978436253482f7352443572773d3d"},{"name":"廉洁修身:大学版","serial":"G641/JC1-2","author":"广东高校《廉洁修身》教材编写组编","publisher":"广东高等教育出版社 2016","macno":"7a6261422b6e4c546332614d736551356b5054794c513d3d"},{"name":"西方经济学,宏观部分.6版","serial":"F091.3/JC4(6D)","author":"高鸿业主编","publisher":"中国人民大学出版社 2014","macno":"6f6233626b37686653716870387934485674553458513d3d"}]}
     */
    public function actionTopBook()
    {
        return Yii::$app->runAction('api/opac/top-book', $this->data);
    }


    /**
     * @api {post} opac/current-book 获取当前借阅书籍
     * @apiVersion 1.0.0
     * @apiName current-book
     * @apiGroup Opac
     *
     * @apiDescription 当前借阅书籍，目前只返回一页的结果
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      当前借阅的书籍结果
     * @apiSuccess {String}     data.barId          条码号
     * @apiSuccess {String}     data.name           书名
     * @apiSuccess {String}     data.author         作者
     * @apiSuccess {String}     data.borrowedTime   借阅时间
     * @apiSuccess {String}     data.returnTime     应归还时间
     * @apiSuccess {int}        data.renewTimes     已续借次数
     * @apiSuccess {String}     data.location       馆藏地
     * @apiSuccess {String}     data.checkId        续借时用到的码
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     * @apiError 3303 图书馆账号已被注销
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"barId":"S1101752","name":"深入理解Java虚拟机:JVM高级特性与最佳实践","author":"周志明著","borrowedTime":"2016-12-10","returnTime":"2017-04-06","renewTimes":1,"location":"广州校区自然科学图书区(N-Z类)","checkId":"806D39EB"},{"barId":"S1604030","name":"TCP/IP详解","author":"(美)[W.R.史蒂文斯]W.Richard Stevens著","borrowedTime":"2016-12-10","returnTime":"2017-04-06","renewTimes":1,"location":"广州校区自然科学图书区(N-Z类)","checkId":"F43A0181"}]}
     */
    public function actionCurrentBook()
    {
        return Yii::$app->runAction('api/opac/current-book', $this->data);
    }

    /**
     * @api {post} opac/borrowed-book 获取历史借阅书籍
     * @apiVersion 1.0.0
     * @apiName borrowed-book
     * @apiGroup Opac
     *
     * @apiDescription 历史借阅书籍
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     *
     * @apiSuccess {int}      code      状态码，0为正常返回
     * @apiSuccess {String}   msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]} data      当前借阅的书籍结果
     * @apiSuccess {String}     data.barId          条码号
     * @apiSuccess {String}     data.name           书名
     * @apiSuccess {String}     data.author         作者
     * @apiSuccess {String}     data.borrowedTime   借阅时间
     * @apiSuccess {String}     data.returnTime     已归还时间
     * @apiSuccess {int}        data.renewTimes     已续借次数，仅为与当前借阅统一用，固定返回999
     * @apiSuccess {String}     data.location       馆藏地
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     * @apiError 3303 图书馆账号已被注销
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":[{"barId":"S1101752","name":"深入理解Java虚拟机:JVM高级特性与最佳实践","author":"周志明著","borrowedTime":"2016-10-11","returnTime":"2016-12-10","renewTimes":999,"location":"广州校区自然科学图书区(N-Z类)"},{"barId":"S1282556","name":"HTML 5用户指南","author":"(美)Bruce Lawson,(美)Remy Sharp著","borrowedTime":"2016-07-12","returnTime":"2016-10-07","renewTimes":999,"location":"广州校区自然科学图书区(N-Z类)"}]}
     */
    public function actionBorrowedBook()
    {
        return Yii::$app->runAction('api/opac/borrowed-book', $this->data);
    }

    /**
     * @api {post} opac/renew-book 续借图书
     * @apiVersion 1.0.0
     * @apiName renew-book
     * @apiGroup Opac
     *
     * @apiDescription 续借图书
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     * @apiParam {String} barId     条码号
     * @apiParam {String} checkId   不明字段，在当前借阅页面里可获取
     * @apiParam {String} verify    验证码
     *
     * @apiSuccess {int}        code      状态码，0为正常返回
     * @apiSuccess {String}     msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}     data      当前借阅的书籍结果
     * @apiSuccess {String}         data.data      续借成功、超过最大续借次数，不得续借！、错误的验证码(wrong check code)
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     * @apiError 3301 参数不完整续借无力
     * @apiError 3303 图书馆账号已被注销
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{"data":"续借成功"}}
     */
    public function actionRenewBook()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['barId']) || !isset($req['checkId']) || !isset($req['verify'])) {
            $req['barId'] = '';
            $req['checkId'] = '';
            $req['verify'] = '';
        }
        $this->data['barId'] = $req['barId'];
        $this->data['checkId'] = $req['checkId'];
        $this->data['verify'] = $req['verify'];
        return Yii::$app->runAction('api/opac/renew-book', $this->data);
    }

    /**
     * @api {post} opac/get-renew-book-verify 获取续借的验证码
     * @apiVersion 1.0.0
     * @apiName get-renew-book-verify
     * @apiGroup Opac
     *
     * @apiDescription 获取续借的验证码
     *
     * @apiParam {String} appid 通用参数，类似参数此处省略
     *
     * @apiSuccess {int}        code      状态码，0为正常返回
     * @apiSuccess {String}     msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object}     data      验证码
     * @apiSuccess {String}         data.data      验证码图片的base64编码
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     * @apiError 3303 图书馆账号已被注销
     *
     * @apiSuccessExample {json} 正常返回
     * {"code":0,"msg":"","data":{"data":"R0lGODdhPAAkAIAAAAQCBNTWzCwAAAAAPAAkAAACdIyPqcvtD6OctNqLs968+w+G4kiW5omm6sq27osB8gwodM3IOW3pjR8AIngJoDByfBx9ySJOopspnwZhs0pFMrMHqXPIDV4p4yW1HCanseD2b72wws1sx3hX7z6bRLf40gfmFZd2A3OImKi4yNjo+AgZaVEAADs="}}
     */
    public function actionGetRenewBookVerify()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        return Yii::$app->runAction('api/opac/get-renew-book-verify', $this->data);
    }

    /**
     * @api {post} opac/get-book-store-detail 获取书本馆藏和借阅状态
     * @apiVersion 1.0.0
     * @apiName get-book-store-detail
     * @apiGroup Opac
     *
     * @apiDescription 返回书本馆藏和借阅状态，从详细信息页获取的
     *
     * @apiParam {String} macno         查看书本详细信息时用到的id
     *
     * @apiSuccess {int}        code      状态码，0为正常返回
     * @apiSuccess {String}     msg       错误信息，code非0时有错误信息提示
     * @apiSuccess {Object[]}   data      验证码
     * @apiSuccess {String}         data.barId          条码号
     * @apiSuccess {String}         data.serial         序列号
     * @apiSuccess {String}         data.volume         年卷期，有"-"，""的情况
     * @apiSuccess {String}         data.location       馆藏地
     * @apiSuccess {String}         data.state          可借状态
     *
     * @apiError 3000 学号或者密码为空
     * @apiError 3001 学号或密码错误
     * @apiError 3302 没有macno参数你也想看书本详情
     *
     * @apiSuccessExample {json} 正常返回
     * [{"barId":"S1836879","serial":"TP312JA/1077","volume":"-","location":"广州校区自然科学图书区(N-Z类)","state":"可借"},{"barId":"S1836880","serial":"TP312JA/1077","volume":"-","location":"三水校区自然科学阅览区","state":"借出-应还日期：2017-04-06"}]
     */
    public function actionGetBookStoreDetail()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['macno'])) {
            $req['macno'] = '';
        }
        $this->data['macno'] = $req['macno'];
        return Yii::$app->runAction('api/opac/get-book-store-detail', $this->data);
    }


    // http://localhost:82/index.php?r=opac/book-detail&macno=0000442705
    // 查看书本详细信息（未登录）,未有需求去实现
    public function actionGetBookDetail()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['macno'])) {
            $req['macno'] = '';
        }
        $this->data['macno'] = $req['macno'];
        // return Yii::$app->runAction('api/opac/book-detail', $this->data);
    }

    //  http://localhost:82/index.php?r=opac/test&sno=1&pwd=2
    public function actionTest()
    {
        return Yii::$app->runAction('api/opac/test');
    }
}
