<?php

namespace dslibs\clinica\historia\models;

use Yii;
use yii\db\Query;
use dslibs\admin\models\MenuModel;
use dslibs\clinica\admin\models\CitaModel;
use dslibs\clinica\admin\models\FinanciadorModel;
use dslibs\clinica\admin\models\PacienteModel;
use dslibs\clinica\admin\models\IqModel;
use yii\db\ActiveRecord;

class EpisodioModel extends ActiveRecord
{
	public static function tableName ()
    {
        return 'episodio';
    }

    public function rules ()
    {
        return [
           [['epis_pac_id'],'required'],
           [['epis_id','epis_pac_id','epis_finan_id','epis_estado','epis_dep'],'integer'],
           [['epis_fechaacc','epis_fechaabre','epis_fechacierra','epis_fdc','epis_fdu', 'epis_userlogin',
                   'epis_pac_id', 'epis_expediente'],'safe'],
           [['epis_observ'],'string','max' => 250],
           [['epis_userlogin', 'epis_expediente'],'string','max' => 45],
           [['epis_id'],'unique'],
           ['epis_estado', 'default', 'value' => '1'],
           ['epis_fechaabre', 'default', 'value' => date('Y-m-d')],
       ];
    }

    public function attributeLabels ()
    {
        return [
                'epis_id' => 'ID',
                'epis_pac_id' => 'Paciente',
                'epis_finan_id' => 'Financiador',
                'epis_estado' => 'Estado del episodio',
                'epis_fechaacc' => 'Fecha del accidente',
                'epis_fechaabre' => 'Fecha de apertura',
                'epis_fechacierra' => 'Fecha de cierre',
                'epis_observ' => 'Observaciones',
                'epis_fdc' => 'Fdc',
                'epis_fdu' => 'Fdu',
                'epis_userlogin' => 'userlogin',
                'epis_dep' => 'Departamento',
                'epis_expediente' => 'Expediente'
        ];
    }

    public function getCitas ()
    {
        return $this->hasMany(CitaModel::className(),['cita_epis_id' => 'epis_id']);
    }

    public function getConsultas ()
    {
        return $this->hasMany(VisitaModel::className(),['consulta_epis_id' => 'epis_id']);
    }

    public function getEpicrisis ()
    {
        return $this->hasMany(EpicrisisModel::className(),['epic_epis_id' => 'epis_id']);
    }

    public function getFinanciador ()
    {
        return $this->hasOne(FinanciadorModel::className(),['finan_id' => 'epis_finan_id']);
    }

    public function getEstado ()
    {
        return $this->hasOne(MenuModel::className(),['m_valor' => 'epis_estado'])->where(['m_ident' => 'estado_episodio']);
    }

    public function getPaciente ()
    {
    	return $this->hasOne(PacienteModel::className(),['pac_id' => 'epis_pac_id']);
    }

    public function getEpisodiodx ()
    {
        return $this->hasMany(EpisodioDxModel::className(),['edx_epis_id' => 'epis_id']);
    }

    public function getDx ()
    {
    	return $this->hasMany(DxModel::className(), ['dx_id' => 'edx_dx_id'])
    	->via('episodiodx');
    }
    
    public function getSubDx ()
    {
    	return $this->hasMany(DxModel::className(), ['dx_id' => 'edx_subdx_id'])
    	->via('episodiodx');
    }

    public function getIq ()
    {
        return $this->hasMany(IqModel::className(),['iq_epis_id' => 'epis_id']);
    }
    
    public function getEpisLista ()
    {
        return Yii::$app->formatter->asDate($this->epis_fdc).': '.$this->estado['m_texto'];
    }

    public static function v_episodio_antec ()
    {
    	return (new Query())
    	->select(['e.epis_id', 'd.dx_descripcion', 'iq.iq_diagnostico',
    	'iq.iq_fecha', 'e.epis_fdc', 'm.m_texto as estado', 'e.epis_estado'])
    	->from('episodio e')
    	->leftJoin('episodiodx ed', 'e.epis_id = ed.edx_epis_id')
    	->leftjoin('dx d', 'ed.edx_dx_id = d.dx_id')
    	->leftjoin('iq', 'e.epis_id = iq.iq_epis_id')
    	->leftJoin('menu m', 'e.epis_estado = m.m_valor and m.m_parent = 241');
    }

    public static function v_episodioPaciente ()
    {
    	return (new Query())->select([
    		"concat_ws(' ', p.pac_nom, p.pac_apell1, p.pac_apell2) as paciente",
    		"date_part('year'::text, age(now(), (p.pac_fnac)::timestamp with time zone)) AS edad",
    		"f.finan_empresa as financiador", "f.finan_id", 'e.epis_id as epis_id', 'e.epis_estado',
    	    'e.epis_pac_id as pac_id', "e.epis_expediente", 'm.m_texto as departamento'
    	])->from('episodio e')
    	->leftJoin('paciente p', 'e.epis_pac_id = p.pac_id')
    	->leftJoin('financiador f', 'e.epis_finan_id = f.finan_id')
    	->leftJoin('menu m', "e.epis_dep = m.m_valor and m_ident = 'departamento'");
    }
    
    public static function v_episodioDx ()
    {
        return (new Query())->select(['e.epis_id', 'd.dx_descripcion', 'e.epis_fdc'])
    	->from('episodio e')
    	->leftJoin('episodiodx ed', 'e.epis_id = ed.edx_epis_id')
    	->leftjoin('dx d', 'ed.edx_dx_id = d.dx_id');
    }

    public static function v_episodioIq ()
    {
    	return (new Query())->select(['e.epis_id', 'iq.iq_diagnostico',
    	'iq.iq_fecha', 'e.epis_fdc', 'e.epis_estado', 'm.m_texto as estado'])
    	->from('episodio e')
    	->leftjoin('iq', 'e.epis_id = iq.iq_epis_id')
    	->leftJoin('menu m', 'e.epis_estado = m.m_id');
    }
}
