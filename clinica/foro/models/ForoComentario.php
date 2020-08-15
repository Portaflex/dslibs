<?php

namespace app\modules\foro\models;

use yii\db\ActiveRecord;
use dslibs\admin\models\UsuarioModel;

class ForoComentario extends ActiveRecord
{
    public static function tableName()
    {
        return 'f_comentario';
    }

    public function rules()
    {
        return [
            [['c_tema_id', 'c_articulo_id', 'c_autor_id'], 'integer'],
            [['c_texto'], 'string'],
            [['c_fdc'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'c_id' => 'C ID',
            'c_tema_id' => 'C Tema ID',
            'c_articulo_id' => 'C Articulo ID',
            'c_autor_id' => 'C Autor ID',
            'c_texto' => 'C Texto',
            'c_fdc' => 'C Fdc',
        ];
    }
    
    public function getAutor ()
    {
        return $this->hasOne(UsuarioModel::className(), ['user_id' => 'c_autor_id']);
    }
    
    public function getArticulo ()
    {
        return $this->hasOne(ForoArticulo::className(), ['a_id' => 'c_articulo_id']);
    }
    
    public function getTema ()
    {
        return $this->hasOne(ForoTema::className(), ['t_id' => 'c_tema_id']);
    }
}
