<?php

namespace app\social\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class ImFeedReply extends ActiveRecord
{

    public static function tableName()
    {
        return 'im_feed_reply';
    }


    public function rules()
    {
        return [
            [['user_id', 'content','parent_id'], 'required'],
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->setAttribute("create_time", time());
                $this->setAttribute("is_deleted", 0);
                $this->setAttribute("up_count", 0);
            }
            return true;
        }
        return false;
    }

    public function listRecentFeedReply($parent_id = 0,$pageNo = 1,$pageNum = 10)
    {
        $offset = ($pageNo - 1)*$pageNum;
        return $this::find()->where('parent_id = '.$parent_id)->orderBy('create_time DESC')
            ->offset($offset)->limit($pageNum)->all();
    }
}
