<?php

/**
 * Esta es la clase implementa el modelo de pacientes.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use dslibs\clinica\historia\models\PacienteAntecModel;
use yii\db\Query;
use dslibs\clinica\historia\models\EpisodioModel;
use yii\db\ActiveRecord;

class PacienteModel extends ActiveRecord
{

    public static function tableName ()
    {
        return 'paciente';
    }

    public function rules ()
    {
        return [
           [['pac_nom','pac_apell1'],'required'],
           [['pac_fnac','pac_fechacreac','pac_fdc','pac_fdu'],'safe'],
           [['pac_observac','pac_antec'],'string'],
           [['pac_activo','pac_recibe', 'pac_remitente'],'integer'],
           [['pac_nom','pac_apell1','pac_apell2','pac_poblac','pac_provincia','pac_telefo','pac_telefo2',
             'pac_userlogin','pac_login','pac_pw','pac_grupo'],'string','max' => 45],
           [['pac_nif'],'string','max' => 10],
           [['pac_numss','pac_email'],'string','max' => 255],
           [['pac_direcc'],'string','max' => 100],
           [['pac_cpostal'],'string','max' => 5],
        	['pac_fnac', 'date', 'format' => 'd-m-Y'],
        ];
    }

    public function attributeLabels ()
    {
        return [
                'pac_id' => 'N.H.',
                'pac_nom' => 'Nombres',
                'pac_apell1' => 'Primer Apellido',
                'pac_apell2' => 'Segundo Apellido',
                'pac_fnac' => 'Fecha de Nacimiento',
                'pac_nif' => 'NIF',
                'pac_numss' => 'Número de SVS',
                'pac_direcc' => 'Dirección',
                'pac_poblac' => 'Población',
                'pac_provincia' => 'Provincia',
                'pac_cpostal' => 'Código postal',
                'pac_telefo' => 'Telefono',
                'pac_telefo2' => 'Telefono',
                'pac_email' => 'E-mail',
                'pac_observac' => 'Observaciones',
                'pac_fechacreac' => 'Pac Fechacreac',
                'pac_fdc' => 'Pac Fdc',
                'pac_fdu' => 'Pac Fdu',
                'pac_userlogin' => 'Pac Userlogin',
                'pac_login' => 'Pac Login',
                'pac_pw' => 'Pac Pw',
                'pac_grupo' => 'Pac Grupo',
                'pac_activo' => 'Pac Activo',
                'pac_recibe' => 'Pac Recibe',
                'pac_antec' => 'Pac Antec',
                'paciente' => 'PacienteModel',
                'pac_remitente' => 'Remitente'
        ];
    }

    public function getNombre()
    {
    	return $this->pac_nom.' '.$this->pac_apell1.' '.$this->pac_apell2;
    }
    
    public function getEdad ()
    {
        if ($this->pac_fnac != '')
        {
            $diff = date_diff(date_create($this->pac_fnac), date_create(date('Y-m-d')));
            return $diff->format('%y');
        }
    }

    public function getCitas ()
    {
        return $this->hasMany(CitaModel::className(),['cita_pac_id' => 'pac_id']);
    }

    public function getEpisodios ()
    {
        return $this->hasMany(EpisodioModel::className(),['epis_pac_id' => 'pac_id'])->orderBy('epis_fdc desc');
    }

    public function getIqs ()
    {
        return $this->hasMany(IqModel::className(),['iq_pac_id' => 'pac_id']);
    }

    public function getAntecedente ()
    {
        return $this->hasMany(PacienteAntecModel::className(),['pa_pac_id' => 'pac_id']);
    }

    public function getAntec ()
    {
    	return $this->hasMany(PacienteAntecModel::className(), ['antec_id' => 'pa_pac_id'])
    		->via('antecedentes');
    }

    public static function v_pacienteantec ()
    {
    	return (new Query())->select([
    			'p.*', 'a.antec_desc as antec', 'pa.pa_txt as texto'])
    			->from('paciente p')
    			->leftjoin('pacienteantec pa', 'pa.pa_pac_id = p.pac_id')
    			->leftjoin('antec a', 'pa.pa_antec_id = a.antec_id');
    }
}
