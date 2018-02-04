<?php
/**
 * User: xiaoguang
 * Date: 2017/11/12
 */

namespace app\social\controllers;

use app\models\Dao_ImFeed;
use app\social\base\Error;
use app\social\base\NoAuthController;
use app\social\models\ImFeed;
use app\social\models\Student;
use yii;
use yii\web\Response;

class UserController extends NoAuthController
{
    protected $expire = 1800;//半小时

    public function actionLogin()
    {

//         timestamp
// sign = md5 参数
// sno 学号
// token 登陆时返回的
// body
// 内容是json串

        $model = new Student();
        $model->load($this->arrInput);
        $model->insertOrUpdate($this->arrInput['user_id']);
        return $this->getReturn(Error::commonHit,"登陆成功",[]);
    }

    public function actionListImFeed($pageNo = 1,$pageNum = 20)
    {
        $model = new ImFeed();
        $ret = $model->listRecentFeed($pageNo,$pageNum);
        return $this->getReturn(Error::success,"",$ret);
    }

    public function actionTest()
    {
        return $this->getReturn(Error::success,$this->parseFewSztz(file_get_contents('F:\\Desktop\\sutuo16.html')));
    }

    /**
     * 获取IDS系统cookie，若有缓存则取，否则登陆
     * @param $sno
     * @param $pwd
     * @return null|string IDS系统的cookie
     */
    protected function getIdsCookie($sno, $pwd)
    {
        $cache = Yii::$app->cache->get(self::REDIS_IDS_PRE . $sno);
        if ($cache) {
            return $cache;
        }
        $idsCookie = $this->loginIdsSys($sno, $pwd);
        if(empty($idsCookie)){
            return null;
        }
        Yii::$app->cache->set(self::REDIS_IDS_PRE . $sno, $idsCookie, $this->expire);
        return $idsCookie;
    }


}