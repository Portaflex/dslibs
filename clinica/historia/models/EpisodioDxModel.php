<?php

namespace dslibs\clinica\historia\models;

class EpisodioDxModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'episodio_dx';
    }

    public function rules()
    {
        return [
            //[['edx_epis_id', 'edx_pac_id', 'edx_dx_id'], 'required'],
            [['edx_id', 'edx_epis_id', 'edx_pac_id', 'edx_dx_id', 'edx_subdx_id'], 'integer'],
            [['edx_txt'], 'string'],
        	[['edx_fdc', 'edx_fdu', 'edx_epis_id', 'edx_pac_id', 'edx_dx_id', 'edx_userlogin'], 'safe'],
            [['edx_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'edx_id' => 'Edx ID',
            'edx_epis_id' => 'Edx Epis ID',
            'edx_pac_id' => 'Edx Pac ID',
            'edx_dx_id' => 'Diagnóstico',
            'edx_txt' => 'Edx Txt',
            'edx_fdc' => '	',
            'edx_fdu' => 'Edx Fdu',
            'edx_userlogin' => 'Edx Userlogin',
            'edx_subdx_id' => 'Edx Subdx ID',
        ];
    }
    public function getEpis()
    {
        return $this->hasOne(EpisodioModel::className(), ['epis_id' => 'edx_epis_id', 'epis_pac_id' => 'edx_pac_id']);
    }

    public function getDx ()
    {
    	return $this->hasOne(DxModel::className(), ['dx_id' => 'edx_dx_id']);
    }

    public function getSubDx ()
    {
    	return $this->hasOne(DxModel::className(), ['dx_id' => 'edx_subdx_id']);
    }
    
    public function getDxLista ()
    {
        return $this->dx['dx_descripcion'].' - '.$this->subDx['dx_descripcion'];
    }
}
