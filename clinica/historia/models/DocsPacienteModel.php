<?php

namespace dslibs\clinica\historia\models;

class DocsPacienteModel extends HistoriaModel
{
   public static function tableName()
    {
        return 'docs';
    }

    public function rules()
    {
        return [
            //[['doc_pac_id', 'doc_epis_id', 'doc_titulo', 'doc_nombre', 'doc_userlogin', 'doc_fdu'], 'required'],
            [['doc_id', 'doc_pac_id', 'doc_epis_id'], 'integer'],
        		[['doc_fdc', 'doc_fdu', 'doc_pac_id', 'doc_epis_id', 'doc_userlogin'], 'safe'],
            [['doc_titulo', 'doc_nombre', 'doc_userlogin'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'doc_id' => 'ID',
            'doc_pac_id' => 'Paciente',
            'doc_epis_id' => 'Episodio',
            'doc_titulo' => 'Titulo',
            'doc_nombre' => 'Nombre',
            'doc_userlogin' => 'Usuario',
            'doc_fdc' => 'Fecha',
            'doc_fdu' => 'Doc Fdu',
        ];
    }
}
