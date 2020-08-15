<?php

/**
 * Esta es la clase implementa el presentador control de agendas
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use dslibs\clinica\admin\models\AgendaModel;
use dslibs\helpers\Camp;
use dslibs\helpers\Lista;
use yii\base\BaseObject;

class AgendaPresenter extends BaseObject
{
    public static function gridAgenda ()
    {
        $agenda = AgendaModel::find()->joinWith(['sanitarios s', 'departamentos d']);
        $dataProvider = new ActiveDataProvider([
            'query' => $agenda,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => [
                    'fecha', 'fecha_fin', 'hora_inicio', 'hora_fin',
                    'tipo', 'intervalo', 'userlogin',
                    'sanitarios.nombre' => [
                        'asc' => ['s.sani_apellido1' => SORT_ASC],
                        'desc' => ['s.sani_apellido1' => SORT_DESC]
                    ],
                    'departamentos.m_texto' => [
                        'asc' => ['d.m_texto' => SORT_ASC],
                        'desc' => ['d.m_texto' => SORT_DESC]
                    ],
                ],
                'defaultOrder' => ['fecha' => SORT_DESC]
            ]
        ]);
        
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
            'caption' => Html::tag('h2', 'Control de agendas').
                Html::a('Nueva a genda', '/clinica/admin/agenda/edit', ['class' => 'btn btn-xs btn-default']),
    		'tableOptions' => ['class' => 'table table-sm table-hover'],
        	'summary' => 'Total: {totalCount}',
        	'columns' => [
        		['attribute' => 'fecha', 'format' => 'date'],
        	    ['attribute' => 'fecha_fin', 'format' => 'date'],
        		['attribute' => 'hora_inicio', 'format' => 'time'],
        		['attribute' => 'hora_fin', 'format' => 'time'],
        		['attribute' => 'intervalo', 'format' => 'time'],
        		['attribute' => 'sanitarios.nombre', 'label' => 'Sanitario'],
        	    ['attribute' => 'departamentos.m_texto', 'label' => 'Departamento'],
        		['attribute' => 'userlogin','label' => 'Usuario'],
        		['attribute' => '', 'content' => function ($model) {
        			return 	Camp::botonAjax('DEL', 'actualiza', '/clinica/admin/agenda/edit',
        			        ['action' => 'delete', 'id' => 'nuevo']).
        					Html::hiddenInput('id', $model->id); }
        		],
        	]
        ]);
        return $out;
    }

    public static function formAgenda ($model)
    {
    	$dapartamento = Lista::listaMenu('departamento');
        
        $out = 	Html::tag('h2', 'Crear nueva agenda')."\n".
        Html::beginForm('/clinica/admin/agenda/edit', 'post')."\n".
        Html::errorSummary($model).
        "<table width = 100%><tr><td>"."\n".
        Camp::datePicker('fecha', $model->fecha, 'Fecha de inicio')."\n".
        Camp::datePicker('fecha_fin', $model->fecha_fin, 'Fecha de fin')."\n".
        Camp::textInput('hora_inicio', $model->hora_inicio, 'Hora de inicio')."\n".
        Camp::textInput('hora_fin', $model->hora_fin, 'Hora de fin')."\n".
        Camp::textInput('intervalo', $model->intervalo, 'Intervalo')."\n".
    	"</td><td align = 'center'>"."\n".
    	Html::tag('h3', 'Días de la semana').
    	Html::checkbox('lunes', $model->lunes, ['label' => 'Lunes']).'<br>'."\n".
    	Html::checkbox('martes', $model->martes, ['label' => 'Martes']).'<br>'."\n".
    	Html::checkbox('miercoles', $model->miercoles, ['label' => 'Miércoles']).'<br>'."\n".
    	Html::checkbox('jueves', $model->jueves, ['label' => 'Jueves']).'<br>'."\n".
    	Html::checkbox('viernes', $model->viernes, ['label' => 'Viernes']).'<br>'."\n".
    	Html::checkbox('sabado', $model->sabado, ['label' => 'Sábado']).'<br>'."\n".
    	Html::checkbox('domingo', $model->domingo, ['label' => 'Domingo']).'<br>'."\n".
    	"</td><td>"."\n".
    	"<table width = 100%><tr><td>"."\n".
    	Camp::dropDownList('departamento', $model->departamento, $dapartamento, 'Departamento y Sanitario', [
    	    'id' => 'menu', 'onChange' => "sub_menu('menu')",
    	    'url' => '/clinica/admin/agenda/sanitario']).'<br>'. "\n".
    	"</td></tr><tr><td>"."\n".
    	'<br>' . Camp::textInput('repeticion', 1, 'Repeticiones (1)')."\n".
    	"</td></tr></table>".
    	"</td></tr></table>".
    	Camp::botonesNormal('/clinica/admin/agenda').
    	Html::endForm();

    	return $out;
    }
}
