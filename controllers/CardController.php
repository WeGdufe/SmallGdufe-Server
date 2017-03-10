<?php
/**
 * User: xiaoguang
 * Date: 2017/2/7
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

//校园卡
class CardController extends BaseController
{

    // 实时余额
    // http://localhost:82/index.php?r=card/current-cash&sno=&pwd=
    public function actionCurrentCash()
    {
        return Yii::$app->runAction('api/card/current-cash', $this->data);
    }

    //  http://localhost:82/index.php?r=card/consume-today&sno=&pwd=&cardNum=
    public function actionConsumeToday()
    {
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        if(isset($req['cardNum'])){ //必备参数，若缺则api/去检测返回
            $this->data['cardNum'] = $req['cardNum'];
        }
        return Yii::$app->runAction('api/card/consume-today', $this->data);
    }

    public function actionTest()
    {
        return Yii::$app->runAction('api/card/test');
    }
}
