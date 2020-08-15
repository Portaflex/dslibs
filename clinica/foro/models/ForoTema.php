<?php

namespace app\modules\foro\models;

class ForoTema extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'f_tema';
    }

    public function rules()
    {
        return [
            [['t_nombre'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            't_id' => 'T ID',
            't_nombre' => 'T Nombre',
        ];
    }
}
