<?php

namespace dslibs\clinica\historia\models;

class DxModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'dx';
    }

    public function rules()
    {
        return [
            //[['dx_id'], 'required'],
            [['dx_id', 'dx_parent'], 'integer'],
        		[['dx_fdc', 'dx_fdu', 'dx_userlogin'], 'safe'],
            [['dx_cietipo', 'dx_ciegrupo', 'dx_ciesubgrupo', 'dx_descripcion', 'dx_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dx_id' => 'ID',
            'dx_parent' => 'Parent',
            'dx_cietipo' => 'Cietipo',
            'dx_ciegrupo' => 'Ciegrupo',
            'dx_ciesubgrupo' => 'Ciesubgrupo',
            'dx_descripcion' => 'Descripcion',
            'dx_fdc' => 'Fdc',
            'dx_fdu' => 'Fdu',
            'dx_userlogin' => 'Userlogin',
        ];
    }
}
