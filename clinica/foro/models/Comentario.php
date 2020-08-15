<?php

namespace app\modules\foro\models;


use yii\db\ActiveRecord;
use dslibs\admin\models\UsuarioModel;

/**
 * This is the model class for table "cdsslp.e_comentario".
 *
 * @property integer $id
 * @property integer $autor
 * @property string $texto
 * @property string $fdc
 */
class Comentario extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'e_comentario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['autor', 'articulo_id'], 'integer'],
            [['texto'], 'string'],
            [['fdc'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'autor' => 'Autor',
            'texto' => 'Texto',
            'fdc' => 'Fdc',
        ];
    }
    
    public function getAutorNombre ()
    {
        return $this->hasOne(UsuarioModel::className(), ['user_id' => 'autor']);
    }
    
    public function getArticulo ()
    {
        return $this->hasOne(Articulo::className(), ['id' => 'articulo_id']);
    }
}
