<?php

namespace dslibs\clinica\historia\models;

class InformeModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'informe';
    }

    public function rules()
    {
        return [
            [['info_id', 'info_epis_id', 'info_pac_id'], 'integer'],
            [['info_fecha', 'info_fdc', 'info_fdu'], 'safe'],
            [['info_texto'], 'string'],
            [['info_destino'], 'string', 'max' => 100],
            [['info_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'info_id' => 'Info ID',
            'info_epis_id' => 'Info Epis ID',
            'info_pac_id' => 'Info Pac ID',
            'info_fecha' => 'Info Fecha',
            'info_destino' => 'Destinatario',
            'info_texto' => 'Info Texto',
            'info_fdc' => 'Fecha',
            'info_fdu' => 'Info Fdu',
            'info_userlogin' => 'Info Userlogin',
        ];
    }
}
