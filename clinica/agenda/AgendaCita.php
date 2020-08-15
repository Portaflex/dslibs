<?php

/**
 * Esta es la clase implementa el presentador control de citas
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 * 
 * @var $c string
 */

namespace dslibs\clinica\agenda;

use Yii;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\base\BaseObject;
use dslibs\admin\models\UsuarioModel;
use dslibs\clinica\admin\models\CitaModel;
use dslibs\helpers\Camp;
use dslibs\helpers\Lista;
use dslibs\clinica\helpers\OpcionClinica;

class AgendaCita extends BaseObject
{
    public function gridCita()
    {
        $dataProvider = (new CitaModel())->searchAgenda();
    	$estado = OpcionClinica::estadoCita();

    	$out = GridView::widget([
    		'dataProvider' => $dataProvider,
    		'summary' => 'Pacientes totales: {totalCount}',
    		'tableOptions' => ['class' => 'table table-sm table-hover'],
    	    'caption' => Html::tag('h2', 'Agenda de citas del '.Yii::$app->formatter->format(Yii::$app->session['cfecha'], 'date')),
    	    'captionOptions' => ['caption-side' => 'top'],
    	    'rowOptions' => function ($model) {
    	            return ["class" => "med_".$model['cita_sani_id']];
    	       },
    		'columns' => [
    					['class' => 'yii\grid\SerialColumn'],
    					['attribute' => 'cita_hora', 'label' => 'Hora', 'content' => function($model, $key, $index) {
    						$cita_id = $model['cita_id'];
    						return Html::button(date('H:i', strtotime($model['cita_hora'])),
    									['class' => 'btn btn-sm btn-outline-light',
    									 'style' => 'font-size:12px; color:black;',
    									 'onClick' => "formulario('$cita_id')",
    									 'url' => Url::to('/clinica/agenda/cita/form'),
    									 'id' => $cita_id, ]).
    							   Html::hiddenInput('cita_id', $model['cita_id']); }
    					],
    					['attribute' => 'espera', 'label' => 'Espera', 'content' => function($model, $key, $index) {
    						if ($model['cita_estado'] == '45') return '';
    						if ($model['cita_estado'] == '233') return '';
    						else return Yii::$app->formatter->asTime($model['espera']); }
    					],
    					['attribute' => 'cita_estado', 'label' => 'Estado', 'content' => function($model, $key, $index) use ($estado) {
    					 	if ($model['cita_pac_id'] !== null)
    					 	{	
    					 	    return Camp::dropDownList('cita_estado', $model['cita_estado'] , $estado, '',
    					 	            ['clase' => $model['estado'], 'onChange' => "actualiza('c_".$index."')",
    					 	             'url' => '/clinica/agenda/cita/cambia-estado', 'id' => 'c_'.$index]); }
    					    }
    					],
    					['attribute' => 'tipo', 'visible' => 'cita_pac_id', 'label' => 'Tipo'],
    					['attribute' => 'finan_empresa', 'label' => 'Financiador'],
    					['attribute' => 'sanitario', 'label' => 'Médico'],
    					['attribute' => 'paciente', 'label' => 'Paciente', 'content' => function($model, $key, $index) {
    							return Html::a($model['paciente'], ['/clinica/historia/visita/', 'e' => $model['epis_id'],
    							    'p' => $model['pac_id'], 'f' => $model['finan_id'],
    							    'd' => $model['cita_dep'], 'ee' => $model['epis_estado'] ]); }
    					],
    					['attribute' => 'edad', 'label' => 'Edad'],
    					['attribute' => '','content' => function($model, $key, $index) {
    						if ($model['cita_observaciones'] != '')
    						{ 	$cita_id = $model['cita_id'];
    							return Camp::boton($model['cita_link'], 'Save', '', ['onClick' => "formulario('$cita_id')",
    							    'url' => Url::to('/clinica/agenda/cita/form'), 'id' => $cita_id, ]); }
    						}
    					],
    			],
    	]);
    	return $out;
    }

    public function formCita($cita)
    {
        $op_estado = OpcionClinica::estadoCita();
        $op_tipo = OpcionClinica::tipoCita();
        $op_sani = OpcionClinica::saniDep(Yii::$app->session['dep']);

    	$out = Html::beginForm('/clinica/agenda/cita/update', 'post', ['enctype' => 'multipart/form-data']);
    	$out .= Html::tag('h2', $cita->paciente['nombre']);
    	$out .= Html::hiddenInput('cita_id', $cita->cita_id);
    	$out .= Html::tag('b', 'Estado').Html::dropDownList('cita_estado', $cita->cita_estado,
    			$op_estado, ['class' => 'form-control input-sm']);
    	$out .= Html::tag('b', 'Tipo').Html::dropDownList('cita_tipo', $cita->cita_tipo,
    			$op_tipo, ['class' => 'form-control input-sm']);
    	$out .= Html::tag('b', 'Sanitario').Html::dropDownList('cita_sani_id', $cita->cita_sani_id,
    			$op_sani, ['class' => 'form-control input-sm']);
    	$out .= Html::tag('b', 'Observaciones').Html::textarea('cita_observaciones',
    			$cita['cita_observaciones'], ['class' => 'form-control input-sm']).Html::tag('br');
    	$out .= Camp::boton('Actualizar', 'actualizar', 'info');
    	if (empty($cita['cita_pac_id']) && Yii::$app->session['e'] && Yii::$app->session['ee'] == 1)
    	{
    	    $out .= Camp::boton('Asignar', 'asignar', 'success');
    	}
    	if (!empty($cita['cita_pac_id'])) $out .= Camp::boton('Desasignar', 'desasignar', 'warning');
    	$out .= Camp::botonDelete();
    	$out .= Html::endForm();
    	return $out;
    }

    public function formSanitario()
    {
        $op_sanitario = Lista::listaSanitario();
        $out = '<div class="card text-center">';
        $out .= '<div class="card-header">Sanitarios</div>'. "\n";
        $out .= "<div class='card-body' style='padding:20px;'>";
    	$out .= Html::beginForm('/cita/sani', 'post');
    	$out .= Html::checkboxList('sani', Yii::$app->session['sani'] ? Yii::$app->session['sani'] : '', $op_sanitario, [
    			'itemOptions' => ['onClick' => 'this.form.submit()']]);
    	$out .= Html::endForm();
    	$out .= "</div></div>";
    	return $out;
    }

    public function citasCount($datos)
    {
    	$result = array();
    	$r = '<div class="card">';
    	$r .= '<div class="card-header" align="center">Citas</div>'. "\n";
    	$r .= "<div class='card-body' align='right' style='padding:20px;'>";
    	foreach ($datos as $d)
    	{
    		if ($d['tipo'] != NULL) $result[] = $d['tipo'];
    		else $result[] = 'No asignadas';
    	}
    	if ($result != NULL)
    	{
    		$cuenta = array_count_values($result);
    		asort($cuenta);
    	}
    	if (isset($cuenta))
    	{
    		foreach ($cuenta as $k => $v)
    		{
    			$r .= $k.': '.$v.'<br>'. "\n";
    		}
    	} else {
    		$r .= 'No hay citas asignadas';
    	}
    	$r .= "</div></div><br> \n";
    	return $r;
    }

    public function citasFinanciador($datos)
    {
    	$result = array();
    	$r = '<div class="card">';
    	$r .= '<div class="card-header" align="center">Financiadores</div>'. "\n";
    	$r .= "<div class='card-body' align='right' style='padding:20px;'>";
    	foreach ($datos as $d)
    	{
    		if ($d['finan_empresa'] != NULL) $result[] = $d['finan_empresa'];
    	}
    	if ($result != NULL)
    	{
    		$cuenta = array_count_values($result);
    		asort($cuenta);
    	}
    	if (isset($cuenta))
    	{
    		foreach ($cuenta as $k => $v)
    		{
    			$r .= $k.': '.$v.'<br>'. "\n";
    		}
    	} else {
    		$r .= 'No hay citas asignadas';
    	}
    	$r .= '</div></div>';
    	return $r;
    }
    
    public function agendaSanitarios()
    {
        if (isset(Yii::$app->session['cfecha']))
        {
            $sani = CitaModel::find()->where(['cita_fecha' => Yii::$app->session['cfecha']])->all();
            if ($sani)
            {
                $op_sanitario = Lista::lista('sanitario', 'sani_id', ['sani_nombres', 'sani_apellido1'], [['in',
                    'sani_id', ArrayHelper::getColumn($sani, 'cita_sani_id')]], false);
                $out = "<div class='card text-center'>";
                $out .= "<div class='card-header'>Sanitarios</div>";
                $out .= "<div class='card-body' style='padding:20px;'>";
                $out .= Html::beginForm('/clinica/agenda/cita/sani', 'post');
                $out .= Html::checkboxList('sani', Yii::$app->session['sani'] ? Yii::$app->session['sani'] : null, $op_sanitario, [
                    'itemOptions' => ['onClick' => 'this.form.submit()']]);
                $out .= Html::endForm();
                $out .= "</div></div><br> \n";
                return $out;
            }
        }
    }
    
    public function agendaDepartamentos ()
    {
        if (isset(Yii::$app->session['userId']))
        {
            $usuario = UsuarioModel::findOne(Yii::$app->session['userId']);
            $lista = ArrayHelper::map($usuario->departamentos, 'm_valor', 'm_texto');
            
            $out = "<div class='card'>";
            $out .= "<div class='card-header'>Departamento</div>";
            $out .= "<div class='card-body' style='padding:20px;'>";
            $out .= Html::beginForm('/clinica/agenda/cita/dep', 'post');
            $out .= Html::checkboxList('dep', isset(Yii::$app->session['dep']) ? Yii::$app->session['dep'] : '', $lista, [
                'itemOptions' => ['onClick' => 'this.form.submit()']]);
            $out .= Html::endForm();
            $out .= "</div></div>";
            return $out;
        }
    }
    
    public function gridCitaPrint ()
    {
        $dataProvider = (new CitaModel())->searchAgenda();
        $dataProvider->pagination->pageSize = 200;
        
        $out = "<!DOCTYPE html>
                <html lang='es-ES'>
                <style media='screen' type='text/css'>
				.grid-view { font-size: small; }
				</style>";
        
        $out .= GridView::widget([
            'dataProvider' => $dataProvider,
            'caption' => Html::tag('h2', 'Agenda de citas del '.Yii::$app->formatter->format(Yii::$app->session['cfecha'], 'date')),
            'summary' => 'Pacientes totales: {totalCount}',
            'columns' => [
                ['attribute' => 'cita_hora', 'format' => 'time'],
                ['attribute' => 'tipo'],
                ['attribute' => 'finan_empresa', 'label' => 'Seguro'],
                ['attribute' => 'sanitario'],
                ['attribute' => 'paciente'],
                ['attribute' => 'telefono', 'label' => 'Teléfono'],
            ],
            'afterRow' => function () { return Html::tag('tr', "<td colspan=6><hr></td><td></td>"); }
            ]);
        return $out;
    }
}
