<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Dao_ImFeed extends BaseDao
{
    public function saveFeed($arrInput)
    {
        $imFeed = new ImFeed();
        $imFeed['sno'] = $arrInput['sno'];
        $imFeed['parent_id'] = 0;
        $imFeed['content'] = $arrInput['content'];
        $imFeed['imgUrls'] = $arrInput['imgUrls'];
        $imFeed['create_time'] = time();
        return $imFeed->save();
    }
}
