<?php

namespace app\social\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class ImFeed extends ActiveRecord
{

    public static function tableName()
    {
        return 'im_feed_reply';
    }


    public function rules()
    {
        return [
            [['user_id', 'content'], 'required'],
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->setAttribute("create_time", time());
                $this->setAttribute("is_deleted", 0);
            }
            return true;
        }
        return false;
    }

    public function listRecentFeed($pageNo = 1,$pageNum = 10)
    {
        $offset = ($pageNo - 1)*$pageNum;
        return $this::find()->orderBy('create_time DESC')
            ->offset($offset)->limit($pageNum)->all();
    }
}
