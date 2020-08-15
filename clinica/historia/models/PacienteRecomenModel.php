<?php

namespace dslibs\clinica\historia\models;

class PacienteRecomenModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'paciente_recomen';
    }

    public function rules()
    {
        return [
            //[['pr_texto', 'pr_recom_id'], 'required'],
            [['pr_recom_id', 'pr_pac_id', 'pr_epis_id'], 'integer'],
            [['pr_fdc', 'pr_titulo'], 'safe'],
            [['pr_texto'], 'string', 'max' => 2044],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pr_id' => 'ID',
            'pr_texto' => 'Texto',
            'pr_recom_id' => 'Pr Recom ID',
            'pr_fdc' => 'Fecha',
            'pr_pac_id' => 'Pr Pac ID',
            'pr_epis_id' => 'Pr Epis ID',
            'pr_titulo' => 'Título',
        ];
    }
}
