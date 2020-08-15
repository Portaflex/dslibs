<?php

namespace dslibs\clinica\historia\models;

use dslibs\clinica\admin\models\EpisodioModel;
use dslibs\admin\models\MenuModel;
use dslibs\clinica\admin\models\PacienteModel;

class EpicrisisModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'epicrisis';
    }

    public function rules()
    {
        return [
            // [['epic_id'], 'required'],
            [['epic_id', 'epic_epis_id', 'epic_pac_id', 'epic_motivoalta', 'epic_recom_id'], 'integer'],
            [['epic_historia', 'epic_intervencion', 'epic_evolucion', 'epic_antec', 'epic_diagnostico', 'epic_procedimiento', 'epic_recom_text'], 'string'],
        	[['epic_fechaingreso', 'epic_fechaalta', 'epic_fechaiq', 'epic_fdc', 'epic_fdu', 'epic_epis_id', 'epic_pac_id'], 'safe'],
            [['epic_notas'], 'string', 'max' => 250],
            [['epic_userlogin'], 'string', 'max' => 45],
            // [['epic_motivoalta'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'epic_id' => 'Epic ID',
            'epic_epis_id' => 'Epic Epis ID',
            'epic_pac_id' => 'Epic Pac ID',
            'epic_motivoalta' => 'Epic Motivoalta',
            'epic_historia' => 'Epic Historia',
            'epic_intervencion' => 'Epic Intervencion',
            'epic_evolucion' => 'Epic Evolucion',
            'epic_fechaingreso' => 'Epic Fechaingreso',
            'epic_fechaalta' => 'Epic Fechaalta',
            'epic_notas' => 'Epic Notas',
            'epic_antec' => 'Epic Antec',
            'epic_diagnostico' => '	',
            'epic_procedimiento' => 'Epic Procedimiento',
            'epic_fechaiq' => 'Epic Fechaiq',
            'epic_recom_id' => 'Epic Recom ID',
            'epic_recom_text' => 'Epic Recom Text',
            'epic_fdc' => 'Epic Fdc',
            'epic_fdu' => 'Epic Fdu',
            'epic_userlogin' => 'Epic Userlogin',
        ];
    }

    public function getEpis()
    {
        return $this->hasOne(EpisodioModel::className(), ['epis_id' => 'epic_epis_id']);
    }

    public function getMotivoalta()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'epic_motivoalta'])->where(['m_ident' => 'motivo_alta']);
    }

    public function getPac()
    {
        return $this->hasOne(PacienteModel::className(), ['pac_id' => 'epic_pac_id']);
    }
}
