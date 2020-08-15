<?php

/**
 * Esta es la clase implementa el modelo de accesos a historias.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

class LogaccesoModel extends AdminModel
{
    public static function tableName()
    {
        return 'logacceso';
    }

    public function rules()
    {
        return [
            //[['la_id'], 'required'],
            [['la_id', 'la_pac_id', 'la_epis_id'], 'integer'],
        	[['la_fecha', 'la_userlogin'], 'safe'],
            [['la_pagina', 'la_tabla', 'la_url'], 'string', 'max' => 255],
            [['la_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'la_id' => 'ID',
            'la_pagina' => 'Página',
            'la_tabla' => '',
            'la_pac_id' => 'Paciente ID',
            'la_epis_id' => 'Episodio ID',
            'la_userlogin' => 'Userlogin',
            'la_fecha' => 'Fecha',
            'la_url' => 'Url',
        ];
    }
}
