<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Feedback extends ActiveRecord
{
    /*
        字段得是protected 或者不写字段，否则存进去会是null
        http://blog.csdn.net/chinajacklb/article/details/49180975

        protected  $sno;
        protected  $content;
        protected  $email;
        protected  $phone;
    */
    public static function tableName()
    {
        return 'feedback';
    }
}
