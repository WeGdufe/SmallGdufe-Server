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
        // Yii::warning(Yii::$app->request->post());
        $req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        $this->sno = $req['sno'];
        $this->pwd = $req['pwd'];
        $this->data = [
            'sno' => $this->sno,
            'pwd' => $this->pwd,
        ];
        Yii::warning($this->data);
        if ($this->sno == null){
            echo "what are u doing?";
        }else {
            return parent::beforeAction($action);
        }
    }
}
