<?php

/**
 * Esta es la clase implementa el modelo agenda. Está ubicada en la
 * sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use dslibs\admin\models\MenuModel;

class AgendaModel extends AdminModel
{
    public static function tableName()
    {
        return 'agenda';
    }

    public function rules()
    {
        return [
            [['fecha', 'fecha_fin', 'hora_inicio', 'hora_fin', 'intervalo', 'sanitario'], 'required'],
            [['id', 'tipo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo',
            'sanitario', 'departamento', 'repeticion'], 'integer'],
        	[['fecha', 'fecha_fin', 'hora_inicio', 'hora_fin', 'intervalo', 'fdc', 'userlogin'], 'safe'],
            [['editar'], 'string', 'max' => 10],
            [['userlogin'], 'string', 'max' => 20],
            [['hora_inicio', 'hora_fin', 'intervalo'], 'time', 'message' => 'El formato de hora debe ser del tipo 00:00']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fecha' => 'Fecha',
            'fecha_fin' => 'Fecha de Fin',
            'hora_inicio' => 'Hora de Inicio',
            'hora_fin' => 'Hora de Fin',
            'tipo' => 'app', 'Tipo',
            'intervalo' => 'Intervalo',
            'editar' => 'Editar',
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miercoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sabado',
            'domingo' => 'Domingo',
            'sanitario' => 'Sanitario',
            'fdc' => 'Fdc',
            'userlogin' => 'Userlogin',
            'departamento' => 'Departamento',
            'repeticion' => 'Repeticion',
        ];
    }

    public function getSanitarios ()
    {
    	return $this->hasOne(SanitarioModel::className(), ['sani_id' => 'sanitario']);
    }
    
    public function getDepartamentos ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'departamento'])->where(['m_ident' => 'departamento']);
    }
}
