<?php
// namespace app\modules\social;
namespace app\social;
use yii\base\Module;

class socialModule extends Module
{

    public $controllerNamespace = 'app\social\controllers';

    public function init()
    {
        parent::init();
        date_default_timezone_set("Asia/Shanghai");
    }
}