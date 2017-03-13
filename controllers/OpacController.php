<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

class OpacController extends BaseController
{


    public function actionAppLogin()
    {
        return 'OpacController';
    }

// http://localhost:82/index.php?r=opac/search-book&sno=13251102217&pwd=118118&bookName=%E8%A7%A3%E5%BF%A7
    public function actionSearchBook()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['bookName'])) {
            return $this->getParmLeakReturn();
        }
        $this->data['bookName'] = $req['bookName'];
        return Yii::$app->runAction('api/opac/search-book', $this->data);
    }

    //  http://localhost:82/index.php?r=opac/current-book&sno=13251102217&pwd=118118
    public function actionCurrentBook()
    {
        return Yii::$app->runAction('api/opac/current-book', $this->data);
    }

    // 借阅历史
    //  http://localhost:82/index.php?r=opac/borrowed-book&sno=13251102217&pwd=118118
    public function actionBorrowedBook()
    {
        return Yii::$app->runAction('api/opac/borrowed-book', $this->data);
    }
    // 续借
    public function actionRenewBook()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['barId']) || !isset($req['checkId']) || !isset($req['verify'])) {
            return $this->getParmLeakReturn();
        }
        $this->data['barId'] = $req['barId'];
        $this->data['checkId'] = $req['checkId'];
        $this->data['verify'] = $req['verify'];
        return Yii::$app->runAction('api/opac/renew-book', $this->data);
    }
    //获取续借的验证码
    public function actionGetRenewBookVerify()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        return Yii::$app->runAction('api/opac/get-renew-book-verify', $this->data);
    }

    // http://localhost:82/index.php?r=opac/book-detail&macno=0000442705
    // 查看书本详细信息（未登录）,未有需求去实现
    public function actionGetBookDetail()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['macno'])) {
            return $this->getParmLeakReturn();
        }
        $this->data['macno'] = $req['macno'];
        // return Yii::$app->runAction('api/opac/book-detail', $this->data);
    }

    // 查看书本馆藏和借阅状态，也是详细信息页获取
    // TP312JA/1077	S1836880	 -	三水校区自然科学阅览区_4楼南    	可借
    public function actionGetBookStoreDetail()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if (!isset($req['macno'])) {
            return $this->getParmLeakReturn();
        }
        $this->data['macno'] = $req['macno'];
        return Yii::$app->runAction('api/opac/get-book-store-detail', $this->data);
    }



    //  http://localhost:82/index.php?r=opac/test&sno=1&pwd=2
    public function actionTest()
    {
        return Yii::$app->runAction('api/opac/test');
    }
}
