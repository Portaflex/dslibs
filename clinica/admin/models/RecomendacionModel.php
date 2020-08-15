<?php

/**
 * Esta es la clase implementa el modelo de recomendaciones al alta.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;


class RecomendacionModel extends AdminModel
{
    public static function tableName()
    {
        return 'recomen';
    }

    public function rules()
    {
        return [
            //[['recom_id'], 'required'],
            [['recom_id', 'recom_tipo'], 'integer'],
            [['recom_text'], 'string'],
            [['recom_fdc', 'recom_fdu'], 'safe'],
            [['recom_descrip'], 'string', 'max' => 100],
            [['recom_descrip', 'recom_tipo'], 'required'],
            [['recom_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'recom_id' => 'ID',
            'recom_descrip' => 'Título de la recomendación',
            'recom_text' => 'Texto',
            'recom_fdc' => 'Fdc',
            'recom_fdu' => 'Fdu',
            'recom_userlogin' => 'Userlogin',
            'recom_tipo' => 'Tipo de recomendación'
        ];
    }
}
