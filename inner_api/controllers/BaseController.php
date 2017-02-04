<?php
namespace app\inner_api\controllers;
use yii\web\Controller;
use Yii;

/**
 * User: xiaoguang
 * Date: 2017/2/3
 */
class BaseController extends Controller
{
    protected $urlConst;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            $this->urlConst = Yii::$app->params;
            return true;
        } else {
            return false;
        }
    }
}