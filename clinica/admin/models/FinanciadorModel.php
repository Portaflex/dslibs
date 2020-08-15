<?php

/**
 * Esta es la clase implementa el modelo de financiadores.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

class FinanciadorModel extends AdminModel
{
    public static function tableName ()
    {
        return 'financiador';
    }

    public function rules ()
    {
        return [
                [['finan_id', 'finan_activo'],'integer'],
        		[['finan_fechainicio','finan_fechafin','finan_fdc','finan_fdu','finan_userlogin'],'safe'],
                [['finan_membrete'],'string'],
                [['finan_empresa','finan_telefono','finan_userlogin'],'string','max' => 45],
                [['finan_gestor','finan_email'],'string','max' => 100]];
    }

    public function attributeLabels ()
    {
        return [
                'finan_id' => 'ID',
                'finan_empresa' => 'Empresa',
                'finan_gestor' => 'Gestor',
                'finan_telefono' => 'Telefono',
                'finan_email' => 'Email',
                'finan_fechainicio' => 'fecha de inicio con esa aseguradora.',
                'finan_fechafin' => 'fecha de fin de contrato con esa aseguradora.',
                'finan_fdc' => 'Finan Fdc',
                'finan_fdu' => 'Finan Fdu',
                'finan_userlogin' => 'Finan Userlogin',
                'finan_membrete' => 'Membrete',
                'finan_activo' => 'Activo'
        ];
    }

    public function getBaremos ()
    {
        return $this->hasMany(BaremoModel::className(),['bar_finan_id' => 'finan_id']);
    }

    public function getEpisodios ()
    {
        return $this->hasMany(EpisodioModel::className(),['epis_finan_id' => 'finan_id']);
    }
}
