<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

class OpacController  extends Controller
{

    public function actionAppLogin()
    {
        return 'OpacController';
    }

    //  http://localhost:81/index.php?r=opac/search-book
    public function actionSearchBook()
    {
        $data = [
            'sno' => '目前可空',
            'pwd' => '目前可空',
            'bookName' => '解忧',
        ];
        return Yii::$app->runAction('api/opac/search-book', $data);
    }

    //  http://localhost:82/index.php?r=opac/current-book
    public function actionCurrentBook()
    {
        $data = [
            'sno' => '13251102217',
            'pwd' => '118118',
            // 'sno' => '13251102210',
            // 'pwd' => 'qq5521140',
        ];
        return Yii::$app->runAction('api/opac/current-book', $data);
    }

    //  http://localhost:82/index.php?r=opac/borrowed-book
    public function actionBorrowedBook(){
        $data = [
            'sno' => '13251102217',
            'pwd' => '118118',
        ];
        return Yii::$app->runAction('api/opac/borrowed-book', $data);
    }

    //  http://localhost:81/index.php?r=opac/search-book
    public function actionTest()
    {

        return Yii::$app->runAction('api/opac/test');
    }
}