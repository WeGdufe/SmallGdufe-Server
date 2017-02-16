<?php
/**
 * User: xiaoguang
 * Date: 2017/2/16
 */

namespace app\controllers;


use Yii;
use yii\base\Controller;

class BaseController extends Controller
{
    protected $sno='';
    protected $pwd='';
    protected $data=[];
    public function beforeAction($action)
    {
        $this->sno = Yii::$app->request->get('sno');
        $this->pwd = Yii::$app->request->get('pwd');
        $this->data = [
            'sno' => $this->sno,
            'pwd' => $this->pwd,
        ];
        if ($this->sno == null){
            echo "what are u doing?";
        }else {
            return parent::beforeAction($action);
        }
    }
}