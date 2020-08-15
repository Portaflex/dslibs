<?php

namespace app\modules\foro\models;

use yii\db\ActiveRecord;
use dslibs\admin\models\UsuarioModel;

class ForoArticulo extends ActiveRecord
{
    public static function tableName()
    {
        return 'f_articulo';
    }

    public function rules()
    {
        return [
            //[['a_tema_id'], 'required'],
            [['a_tema_id', 'a_autor_id', 'a_estado'], 'integer'],
            [['a_texto'], 'string'],
            [['a_fdc'], 'safe'],
            [['a_titulo'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'a_id' => 'ID',
            'a_tema_id' => 'ID del Tema',
            'a_autor_id' => 'ID del Autor',
            'a_titulo' => 'Título',
            'a_texto' => 'Texto',
            'a_fdc' => 'Fecha',
            'a_estado' => 'Estado',
        ];
    }
    
    public function getAutor ()
    {
        return $this->hasOne(UsuarioModel::className(), ['user_id' => 'a_autor_id']);
    }
    
    public function getTema ()
    {
        return $this->hasOne(ForoTema::className(), ['t_id' => 'a_tema_id']);
    }
}
