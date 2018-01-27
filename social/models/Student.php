<?php

namespace app\social\models;

use yii\db\ActiveRecord;

class Student extends ActiveRecord
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
        ];
    }

    public function queryUserExist($user_id)
    {
        return !empty($this::findOne(
            ['user_id' => $user_id]
        ));
    }
    public function queryUserByUserId($user_id)
    {
        return $this::findOne(['user_id' => $user_id]);
    }

    public function insertOrUpdate($user_id)
    {
        if ($this->queryUserExist($user_id)) {
            $this::updateAll(['last_login_time' => time()]);
        } else {
            $this->setAttribute("create_time", time());
            $this->setAttribute("last_login_time", time());
            $this::save();
        }
    }

}
