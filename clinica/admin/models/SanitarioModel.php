<?php

/**
 * Esta es la clase implementa el modelo de sanitarios.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use dslibs\admin\models\AdminModel;
use dslibs\admin\models\MenuModel;

class SanitarioModel extends AdminModel
{
    public static function tableName()
    {
        return 'sanitario';
    }

    public function rules()
    {
        return [
            [['sani_id', 's_id', 'sani_agenda', 'sani_opera'], 'integer'],
            [['sani_fdc', 'sani_fdu'], 'safe'],
            [['sani_nombres', 'sani_apellido1', 'sani_apellido2', 'sani_especialidad',
              'sani_dni', 'sani_ncolegiado', 'sani_telefono', 'sani_e_mail', 'sani_group',
              'sani_userlogin'], 'string', 'max' => 45],
            [['sani_nombres', 'sani_apellido1', 'sani_e_mail'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sani_id' => 'ID',
            's_id' => 'ID',
            'sani_nombres' => 'Nombres',
            'sani_apellido1' => 'Primer apellido',
            'sani_apellido2' => 'Segundo apellido',
            'sani_especialidad' => 'Especialidad',
            'sani_dni' => 'Dni',
            'sani_ncolegiado' => 'Nº colegiado',
            'sani_telefono' => 'Telefono',
            'sani_e_mail' => 'E-Mail',
            'sani_group' => 'Grupo de usuarios',
            'sani_fdc' => 'Fdc',
            'sani_fdu' => 'Fdu',
            'sani_userlogin' => 'Userlogin',
            'sani_agenda' => 'Agenda',
            'sani_color' => 'Color',
            'sani_opera' => 'Opera',
        ];
    }

    public function getNombre()
    {
    	return $this->sani_nombres . ' ' . $this->sani_apellido1 . ' ' . $this->sani_apellido2;
    }
    
    public function getApellido ()
    {
        return $this->sani_apellido1;
    }

    public function getCitas()
    {
        return $this->hasMany(CitaModel::className(), ['cita_sani_id' => 'sani_id']);
    }

    public function getIqsrol()
    {
        return $this->hasMany(IqRolModel::className(), ['iqrol_sani_id' => 'sani_id']);
    }
    
    public function getSanitarioDep()
    {
        return $this->hasMany(SanitarioDepModel::className(), ['sd_sani_id' => 'sani_id']);
    }
    
    public function getSaniAgenda ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'sani_agenda'])->where(['a.m_ident' => 'booleano']);
    }
    
    public function getSaniOpera ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'sani_opera'])->where(['o.m_ident' => 'booleano']);
    }
}
