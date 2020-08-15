<?php

/**
 * Esta es la clase implementa el modelo de intervenciones.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use dslibs\admin\models\MenuModel;

class IqModel extends AdminModel
{
    const SCENARIO_HISTORIA = 'historia';
    
    public static function tableName()
    {
        return 'iq';
    }

    public function rules()
    {
        return [
            [['iq_epis_id', 'iq_pac_id', 'iq_norden', 'iq_estado', 'iq_tipo', 'iq_ingreso_tipo',
                'iq_quirofano', 'iq_anestesia', 'iq_dep'], 'integer'],
        	[['iq_fecha', 'iq_hora', 'iq_hentra', 'iq_hsale', 'iq_fdc', 'iq_fdu', 'iq_epis_id',
        	    'iq_pac_id', 'iq_userlogin', 'iq_dep', 'iq_observ', 'iq_codigo'], 'safe'],
            [['iq_hora'], 'time', 'on' => self::SCENARIO_HISTORIA],
            ['iq_estado', 'default', 'value' => 1],
            [['iq_protocolo'], 'string'],
            [['iq_diagnostico', 'iq_procedimiento'], 'string', 'max' => 200],
            [['iq_userlogin'], 'string', 'max' => 45],
            [['iq_fecha', 'iq_hora', 'iq_estado'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'iq_id' => 'ID',
            'iq_epis_id' => 'Episodio',
            'iq_pac_id' => 'Paciente ID',
            'iq_fecha' => 'Fecha',
            'iq_hora' => 'Hora',
            'iq_hentra' => 'Hora de entrada',
            'iq_hsale' => 'Hora de salida',
            'iq_norden' => 'Norden',
            'iq_estado' => 'Estado',
            'iq_tipo' => 'Tipo',
            'iq_ingreso_tipo' => 'Ingreso Tipo',
            'iq_quirofano' => 'Quirofano',
            'iq_diagnostico' => 'Diagnostico',
            'iq_procedimiento' => 'Procedimiento',
            'iq_anestesia' => 'Anestesia',
            'iq_protocolo' => 'Protocolo',
            'iq_fdc' => 'Fdc',
            'iq_fdu' => 'Fdu',
            'iq_userlogin' => 'Userlogin',
            'iq_dep' => 'Departamento',
            'iq_observ' => 'Observaciones',
            'iq_codigo' => 'Codigo autorización'
        ];
    }

    public function getEpisodio()
    {
        return $this->hasOne(EpisodioModel::className(), ['epis_id' => 'iq_epis_id']);
    }

    public function getEstado()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'iq_estado'])->where(['m_ident' => 'estado_iq']);
    }

    public function getTipo()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'iq_ingreso_tipo'])->where(['m_ident' => 'tipo_ingreso']);
    }

    public function getPaciente()
    {
        return $this->hasOne(PacienteModel::className(), ['pac_id' => 'iq_pac_id']);
    }

    public function getIqsrols()
    {
        return $this->hasMany(IqRolModel::className(), ['iqsrol_iq_id' => 'iq_id']);
    }
    
    public function getFinanciador ()
    {
        return $this->hasOne(FinanciadorModel::className(), ['finan_id' => 'epis_finan_id'])
            ->via('episodio');
    }
}
