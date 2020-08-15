<?php

namespace app\modules\foro\models;

use yii\db\ActiveRecord;
use dslibs\admin\models\UsuarioModel;

/**
 * This is the model class for table "cdsslp.e_articulo".
 *
 * @property integer $id
 * @property string $titulo
 * @property string $texto
 * @property integer $autor
 * @property string $fdc
 */
class Articulo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'e_articulo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['texto'], 'string'],
            [['autor'], 'integer'],
            [['fdc'], 'safe'],
            [['titulo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'texto' => 'Texto',
            'autor' => 'Autor',
            'fdc' => 'Fdc',
        ];
    }
    
    public function getAutorNombre ()
    {
        return $this->hasOne(UsuarioModel::className(), ['user_id' => 'autor']);
    }
}
