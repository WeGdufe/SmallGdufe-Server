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

    //http://localhost:81/index.php?r=opac/search-book
    public function actionSearchBook()
    {
        $data = [
            'sno' => '目前可空',
            'pwd' => '目前可空',
            'bookName' => '解忧',
        ];
        return Yii::$app->runAction('api/opac/search-book', $data);
    }

}