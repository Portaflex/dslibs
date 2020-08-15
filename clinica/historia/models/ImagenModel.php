<?php

namespace dslibs\clinica\historia\models;

use dslibs\clinica\admin\models\PacienteModel;

class ImagenModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'imagen';
    }

    public function rules()
    {
        return [
            //[['image_pac_id', 'image_epis_id', 'image_nombre', 'image_imagen', 'image_userlogin'], 'required'],
            [['image_id', 'image_pac_id', 'image_epis_id'], 'integer'],
            [['image_fdc', 'image_fdu'], 'safe'],
            [['image_nombre', 'image_imagen'], 'string', 'max' => 255],
            [['image_userlogin'], 'string', 'max' => 45],
            [['image_pac_id'], 'exist', 'skipOnError' => true, 'targetClass' => PacienteModel::className(), 'targetAttribute' => ['image_pac_id' => 'pac_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'image_id' => 'ID',
            'image_pac_id' => 'Image Pac ID',
            'image_epis_id' => 'Image Epis ID',
            'image_nombre' => 'Nombre',
            'image_imagen' => '',
            'image_fdc' => 'Fecha',
            'image_fdu' => 'Image Fdu',
            'image_userlogin' => 'Image Userlogin',
        ];
    }

    public function getImagePac()
    {
        return $this->hasOne(PacienteModel::className(), ['pac_id' => 'image_pac_id']);
    }
}
