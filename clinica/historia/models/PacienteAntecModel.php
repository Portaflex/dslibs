<?php

namespace dslibs\clinica\historia\models;

use dslibs\clinica\admin\models\AntecedenteModel;
use dslibs\clinica\admin\models\PacienteModel;

class PacienteAntecModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'paciente_antec';
    }

    public function rules()
    {
        return [
            [['pa_id', 'pa_pac_id', 'pa_antec_id'], 'integer'],
        	[['pa_fdc', 'pa_fdu', 'pa_pac_id', 'pa_userlogin'], 'safe'],
            [['pa_txt'], 'string', 'max' => 250],
            [['pa_userlogin'], 'string', 'max' => 45],
            ['pa_antec_id', 'default', 'value' => 12],
            //[['pa_antec_id'], 'exist', 'skipOnError' => true, 'targetClass' => Antec::className(), 'targetAttribute' => ['pa_antec_id' => 'antec_id']],
            //[['pa_pac_id'], 'exist', 'skipOnError' => true, 'targetClass' => Paciente::className(), 'targetAttribute' => ['pa_pac_id' => 'pac_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pa_id' => 'Pa ID',
            'pa_pac_id' => 'Pa Pac ID',
            'pa_antec_id' => 'Pa Antec ID',
            'pa_txt' => 'Descripción',
            'pa_fdc' => 'Pa Fdc',
            'pa_fdu' => 'Pa Fdu',
            'pa_userlogin' => 'Pa Userlogin',
            'antecLista' => '',
        ];
    }

    public function getAntec()
    {
        return $this->hasOne(AntecedenteModel::className(), ['antec_id' => 'pa_antec_id']);
    }

    public function getPac()
    {
        return $this->hasOne(PacienteModel::className(), ['pac_id' => 'pa_pac_id']);
    }
    
    public function getAntecLista ()
    {
        $out = $this->antec['antec_desc'];
        if (! empty($this->pa_txt)) $out .= ': '.$this->pa_txt;
        return $out;
    }
}
