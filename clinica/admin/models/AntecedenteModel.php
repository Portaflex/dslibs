<?php

/**
 * Esta es la clase implementa el modelo antecedente del paciente.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

class AntecedenteModel extends AdminModel
{
    public static function tableName()
    {
        return 'antecedente';
    }

    public function rules()
    {
        return [
            [['antec_fdc', 'antec_fdu'], 'safe'],
            [['antec_desc', 'antec_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'antec_id' => 'Antec ID',
            'antec_desc' => 'Antec Desc',
            'antec_fdc' => 'Antec Fdc',
            'antec_userlogin' => 'Antec Userlogin',
            'antec_fdu' => 'Antec Fdu',
        ];
    }
}
