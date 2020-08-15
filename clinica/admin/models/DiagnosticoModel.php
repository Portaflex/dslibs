<?php

/**
 * Esta es la clase implementa el modelo de diagnósticos posibles.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

class DiagnosticoModel extends AdminModel
{
    public static function tableName()
    {
        return 'dx';
    }

    public function rules()
    {
        return [
            [['dx_id', 'dx_parent'], 'integer'],
        	[['dx_fdc', 'dx_fdu', 'dx_userlogin'], 'safe'],
            [['dx_cietipo', 'dx_ciegrupo', 'dx_ciesubgrupo',
              'dx_descripcion', 'dx_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dx_id' => 'Dx ID',
            'dx_parent' => 'Dx Parent',
            'dx_cietipo' => 'Dx Cietipo',
            'dx_ciegrupo' => 'Dx Ciegrupo',
            'dx_ciesubgrupo' => 'Dx Ciesubgrupo',
            'dx_descripcion' => 'Dx Descripcion',
            'dx_fdc' => 'Dx Fdc',
            'dx_fdu' => 'Dx Fdu',
            'dx_userlogin' => 'Dx Userlogin',
        ];
    }
}
