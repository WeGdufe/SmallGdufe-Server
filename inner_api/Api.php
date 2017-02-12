<?php

namespace app\inner_api;
use yii\base\Module;
/**
 * api module 入口文件
 */
class Api extends Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\inner_api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        date_default_timezone_set("Asia/Shanghai");
        // custom initialization code goes here
    }
}
