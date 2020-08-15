<?php

namespace dslibs\clinica\historia\models;


class ConsentimientoModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'paciente_ci';
    }

    public function rules()
    {
        return [
            [['pci_id', 'pci_pac_id', 'pci_epis_id'], 'integer'],
            [['pci_texto'], 'string'],
        	[['pci_pac_id', 'pci_epis_id', 'pci_userlogin'], 'safe'],
            [['pci_firma'], 'string', 'max' => 11],
            [['pci_procedimiento', 'pci_userlogin'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pci_id' => 'ID',
            'pci_pac_id' => 'Paciente',
            'pci_epis_id' => 'Episodio',
            'pci_firma' => 'Firma',
            'pci_procedimiento' => 'Procedimiento',
            'pci_texto' => 'Texto',
            'pci_userlogin' => 'Userlogin',
        ];
    }
}
