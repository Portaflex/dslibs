<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use dslibs\clinica\admin\models\IqModel;
use dslibs\clinica\admin\models\IqRolModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\clinica\helpers\OpcionClinica;

class IqPresenter extends BaseObject
{
    public function gridIq ()
    {
    	$dataProvider = new ActiveDataProvider(['query' => IqModel::find()->where(['iq_epis_id' => Yii::$app->session['e']])]);
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	'caption' => Html::tag('h2', 'Intervenciones del Paciente').
        			Camp::botonReturn('/clinica/historia/iq/edit', 'Nueva intervención'),
        	'summary' => '',
        	'tableOptions' => ['class' => 'table table-sm'],
        	'columns' => [
        		['attribute' => 'iq_fecha', 'label' => 'Fecha', 'format' => 'date', 'content' => function ($model) {
        			return Html::a(Yii::$app->formatter->asDate($model->iq_fecha),
        					Url::to(['/clinica/historia/iq/edit', 'id' => $model->iq_id]));
        			}],
        		['attribute' => 'iq_diagnostico', 'label' => 'Diagnóstico',
        		],
        		['attribute' => '', 'label' => 'Ver PDF',
        		 'content' => function ($model) { return Html::a('PDF', ['/clinica/historia/iq/create-pdf',
        		 		'id' => $model->iq_id]); },
        		],
        		['attribute' => '', 'label' => 'Cuardar documento PDF',
        		 'content' => function ($model) { return Html::a('Guardar', ['/clinica/historia/iq/create-pdf',
        		 		'id' => $model->iq_id, 'guardar' => true]); },
        												],
        												]
        ]);
        return $out;
    }

    public function formIq ($model)
    {
    	$quirofano = OpcionClinica::quirofano();
    	$ingresoTipo = OpcionClinica::tipoIngreso();
    	$iqEstado = OpcionClinica::estadoIq();

    	$out = Html::tag('h2', 'Detalles de la intervención').
    	Html::beginForm(Url::to('/clinica/historia/iq/edit'), 'post').
    	Html::errorSummary($model).
    	Html::hiddenInput('iq_id', $model->iq_id).
    	"<div class='row'><div class='col-sm-4'><p>" . "\n".
    	Camp::datePicker('iq_fecha', $model->iq_fecha, 'Fecha').
    	Camp::textInput('iq_hora', Yii::$app->formatter->asTime($model->iq_hora), 'Hora').
    	"</div><div class='col-sm-4'><p>" . "\n".
    	Camp::textInput('iq_norden', $model->iq_norden, 'Nº Orden').
    	Camp::dropDownList('iq_quirofano', $model->iq_quirofano, $quirofano, 'Quirófano').
    	"</p></div><div class='col-sm-4'><p>" . "\n".
    	Camp::dropDownList('iq_ingreso_tipo', $model->iq_ingreso_tipo, $ingresoTipo, 'Tipo de Ingreso').
    	Camp::textInput('iq_codigo', $model->iq_codigo, 'Código autorización').
    	"</p></div>".
    	Camp::textInput('iq_diagnostico', $model->iq_diagnostico, 'Diagnóstico').
    	Camp::textInput('iq_procedimiento', $model->iq_procedimiento, 'Procedimiento').
    	Camp::textInput('iq_observ', $model->iq_observ, 'Observaciones').
    	Camp::ckeditor('iq_protocolo', $model->iq_protocolo, 'Protocolo quirúrgico').
    	"<div class='col-sm-6'><p>" . "\n".
    	Camp::dropDownList('iq_estado', $model->iq_estado, $iqEstado, 'Estado', ['clase' => $model->estado['m_texto']]).
    	"</p></div><div class='col-sm-6'><p>" . "\n".
    	Camp::botonesNormal('/clinica/historia/iq', $model->iq_id).
    	"</p></div></div>".
    	Html::endForm();

    	return $out;
    }

    public function gridSaniIq ($id = false)
    {
        $rol = OpcionClinica::saniRol();
        $sanitario = OpcionClinica::saniOpera();

    	$dataProvider = new ActiveDataProvider(['query' => IqRolModel::find()->where(['iqrol_iq_id' => $id])]);
    	$out = GridView::widget([
    		'dataProvider' => $dataProvider,
    	    'showFooter' => true,
    	    'footerRowOptions' => ['class' => 'panel panel-info'],
    		'tableOptions' => ['class' => 'table table-sm'],
    		'summary' => '',
    		'columns' => [
    			['attribute' => 'iqrol_rol_id', 'label' => 'Rol', 'content' => function ($model) use ($rol) {
    				return Camp::dropDownList('iqrol_rol_id', $model->iqrol_rol_id, $rol).
    						Html::hiddenInput('iqrol_id', $model->iqrol_id).
    						Html::hiddenInput('iqrol_iq_id', $model->iqrol_iq_id); },
    				'footer' => Camp::dropDownList('iqrol_rol_id', '', $rol)
    			],
    			['attribute' => 'iqrol_sani_id', 'label' => 'Sanitario', 'content' => function ($model) use ($sanitario) {
    				return Camp::dropDownList('iqrol_sani_id', $model->iqrol_sani_id, $sanitario); },
    				'footer' => Camp::dropDownList('iqrol_sani_id', '', $sanitario)
    			],
    			['attribute' => '', 'content' => function ($model) use ($id) {
    				return 	Camp::botonesAjax('/historia/iq/edit-sani', 'actualiza'); },
    			'footer' => Html::hiddenInput('iqrol_iq_id', $id).
    						Camp::botonAjax('Guardar', 'actualiza', '/clinica/historia/iq/edit-sani')
    			],
    		]
    	]);
    	return $out;
    }
}
