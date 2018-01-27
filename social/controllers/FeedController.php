<?php
/**
 * User: xiaoguang
 * Date: 2017/11/12
 */

namespace app\social\controllers;

use app\social\base\BaseController;
use app\social\base\Error;
use app\social\models\ImFeed;
use app\social\models\ImFeedReply;
use yii;
use yii\web\Response;

class FeedController extends BaseController
{

    public function actionCreateImFeed($user_id,$content,$imgUrls = '')
    {
        $model = new ImFeed();
        $model->setAttributes($this->arrInput);
        if (!$model->save()) {
            return $this->getReturn(Error::commonHit, "发布失败", []);
        }
        return $this->getReturn(Error::success,"发布成功",[]);
    }

    public function actionCreateImFeedReply($user_id,$content,$imgUrls = '',$parent_id,$target_user_id = 0)
    {
        $model = new ImFeedReply();
        $model->setAttributes($this->arrInput);
        if (!$model->save()) {
            return $this->getReturn(Error::commonHit, "回复失败", []);
        }
        return $this->getReturn(Error::success,"回复成功",[]);
    }

    public function actionListImFeed($pageNo = 1,$pageNum = 20)
    {
        $model = new ImFeed();
        $ret = $model->listRecentFeed($pageNo,$pageNum);
        return $this->getReturn(Error::success,"",$ret);
    }

    public function actionListImFeedReply($parent_id ,$pageNo = 1,$pageNum = 20)
    {
        $model = new ImFeedReply();
        $ret = $model->listRecentFeedReply($parent_id,$pageNo,$pageNum);
        return $this->getReturn(Error::success,"",$ret);
    }

    public function actionTest()
    {
        return $this->getReturn(Error::success,$this->parseFewSztz(file_get_contents('F:\\Desktop\\sutuo16.html')));
    }



}