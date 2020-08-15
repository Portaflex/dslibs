<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use dslibs\admin\models\UsuarioModel;
use dslibs\clinica\admin\models\PacienteModel;
use dslibs\clinica\historia\models\EpisodioModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\clinica\helpers\OpcionClinica;

class EpisodioPresenter extends BaseObject
{
    public function formPaciente()
    {
    	$paciente = PacienteModel::find()->where(['pac_id' => Yii::$app->session['p']])->one();
    	$op_remitente = OpcionClinica::remitente();

    	$out = "<div class='card'><div class='card-header'>" .
        Html::tag('h2', 'Datos del Paciente') .	"</div></div>"."\n".
        Html::beginForm('/clinica/historia/episodio/edit-paciente', 'post').
    	"<div class='row'>".
    	"<div class='col-sm-6'>" . "\n".
    	Html::hiddenInput('pac_id', $paciente['pac_id']).
    	Camp::textInput('pac_nom', $paciente['pac_nom'], 'Nombres').
    	Camp::textInput('pac_apell1', $paciente['pac_apell1'], 'Primer apellido').
    	Camp::textInput('pac_apell2', $paciente['pac_apell2'], 'Segundo apellido').
    	Camp::datePicker('pac_fnac', $paciente['pac_fnac'], 'Fecha de nacimiento').
    	Camp::textInput('pac_nif', $paciente['pac_nif'], 'NIF ').
    	Camp::textInput('pac_direcc', $paciente['pac_direcc'], 'Dirección ').
    	Camp::textInput('pac_poblac', $paciente['pac_poblac'], 'Población ').
    	//Camp::textInput('pac_provincia', $paciente['pac_provincia'], 'Provincia ').
    	"</div>".
    	"<div class='col-sm-6'>"."\n".
    	Camp::textInput('pac_cpostal', $paciente['pac_cpostal'], 'Código postal ').
    	Camp::textInput('pac_telefo', $paciente['pac_telefo'], 'Teléfono').
    	Camp::textInput('pac_telefo2', $paciente['pac_telefo2'], 'Teléfono 2').
    	Camp::textInput('pac_email', $paciente['pac_email'], 'E-mail').
    	Camp::dropDownList('pac_remitente', $paciente['pac_remitente'], $op_remitente, 'Remitente').
    	Camp::textArea('pac_observac', $paciente['pac_observac'], 'Observaciones').
    	"</div></div></div>".
    	Camp::botonesNormal('/clinica/historia/episodio', Yii::$app->session['p']).
    	Html::endForm();
    	
    	return $out;
    }

    public function gridEpisodio()
    {
    	$financiador = OpcionClinica::financiador();
    	$estado = OpcionClinica::estadoEpisodio();
    	
    	$dep = UsuarioModel::findOne(Yii::$app->session['userId']);
    	$deps = ArrayHelper::getColumn($dep->departamentos, 'm_valor');
    	$departamento = ArrayHelper::map($dep->departamentos, 'm_valor', 'm_texto');

    	$dataProvider = new ActiveDataProvider([
    	   'query' => EpisodioModel::find()->with('financiador')
    	               ->andWhere(['epis_pac_id' => $_SESSION['p']])
    	               ->andWhere(['epis_dep' => $deps])->orderBy('epis_fdc DESC'),
    	]);

    	$out = "<div class='card'><div class='card-header'>" .
        	Html::tag('h2', 'Episodios del paciente') . "</div>";
    	$out .= GridView::widget([
    			'dataProvider' => $dataProvider,
    			'tableOptions' => ['class' => 'table table-sm'],
    			'showFooter' => true,
    			'footerRowOptions' => ['class' => 'footer'],
    			'summary' => '',
    			'columns' => [
    			    ['content' => function($model){
    			        return  Html::hiddenInput('epis_id', $model->epis_id).
    			        Html::a("<i class='fas fa-archive' style='font-size:18px;color:#2e8fd8'></i>",
    			                ['/clinica/historia/visita', 'e' => $model->epis_id, 'p' => $model->epis_pac_id,
    			                'f' => $model->epis_finan_id, 'd' => $model->epis_dep,
    			                'ee' => $model->epis_estado], ['class' => 'btn btn-xs btn-default']); }
    			    ],
    				['attribute' => 'epis_fechaabre', 'label' => 'Fecha', 'format' => 'date',
    				 'footer' => Camp::datePicker('epis_fechaabre', '', '', ['style'=>'width:80px;'])
    				],
    				['attribute' => 'epis_expediente', 'content' => function($model){
    				    return 	Camp::textInput('epis_expediente', $model->epis_expediente, '', ['style'=>'width:100px;']); },
    				 'footer' => Camp::textInput('epis_expediente', '', '', ['style'=>'width:100px;'])
    				],
    				['attribute' => 'epis_finan_id', 'content' => function ($model) use ($financiador){
    					return Camp::dropDownList('epis_finan_id', $model->epis_finan_id, $financiador); },
    				 'footer' => Camp::dropDownList('epis_finan_id', '', $financiador)
    				],
    				['attribute' => 'epis_dep', 'label' => 'Departamento', 'content' => function ($model) use ($departamento){
    					return Camp::dropDownList('epis_dep', $model->epis_dep, $departamento); },
    				 'footer' => Camp::dropDownList('epis_dep', '', $departamento)
    				],
    				['attribute' => 'epis_estado', 'label' => 'Estado', 'content' => function ($model) use ($estado){
    					return Camp::dropDownList('epis_estado', $model->epis_estado, $estado); },
    				 'footer' => Camp::dropDownList('epis_estado', '', $estado).
    				            Html::hiddenInput('epis_pac_id', Yii::$app->session['p'])
    				],
    				['content' => function($model){
    					return 	Camp::botonesAjax('/clinica/historia/episodio/edit-episodio', 'actualiza_recarga'); },
	    			 'footer' => Camp::botonAjax('Nuevo', 'actualiza_recarga', '/clinica/historia/episodio/edit-episodio',
	    			         ['class' => 'info', 'action' => 'save'])
	    			]
    			],
    	]);
    	$out .= "</div><br>";
    	return $out;
    }
}
