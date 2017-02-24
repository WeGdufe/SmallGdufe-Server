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
        if (isset($req['bookName'])) {
            $this->data['bookName'] = $req['bookName'];
        }
        // $this->data['bookName'] = Yii::$app->request->post('bookName');
        return Yii::$app->runAction('api/opac/search-book', $this->data);
    }

    //  http://localhost:82/index.php?r=opac/current-book&sno=13251102217&pwd=118118
    public function actionCurrentBook()
    {
        return Yii::$app->runAction('api/opac/current-book', $this->data);
    }

    //  http://localhost:82/index.php?r=opac/borrowed-book&sno=13251102217&pwd=118118
    public function actionBorrowedBook()
    {
        return Yii::$app->runAction('api/opac/borrowed-book', $this->data);
    }

    //  http://localhost:82/index.php?r=opac/search-book&sno=13251102217&pwd=118118
    public function actionTest()
    {
        return Yii::$app->runAction('api/opac/test');
    }
}
