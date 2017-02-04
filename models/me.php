<?php
namespace app\models;
// use yii\db\ActiveRecord;

class Me extends \yii\db\ActiveRecord
{
       /**
     * @inheritdoc
     */
    public static function tableName()
    {
            return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
            return [
                [['idUser'], 'required'],
            [['idUser'], 'integer'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
            return [
                'idUser' => 'Id User',
            'name' => 'Name',
        ];
    }
}
?>