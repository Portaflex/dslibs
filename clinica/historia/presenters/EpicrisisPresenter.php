<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\clinica\historia\models\EpicrisisModel;
use dslibs\clinica\helpers\OpcionClinica;

class EpicrisisPresenter extends BaseObject
{
    public function gridEpicrisis ()
    {
        $recomen = OpcionClinica::recomAlta();
        $query = EpicrisisModel::find()->where(['epic_epis_id' => Yii::$app->session['e']]);
        $dataProvider = new ActiveDataProvider([
        		'query' => $query,
        		'pagination' => ['pageSize' => 30]
        ]);

        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	'summary' => '',
            'caption' => Html::tag('h2', 'Epicrisis del Paciente'),
        	'tableOptions' => ['class' => 'table table-sm'],
        	'showFooter' => true,
        	'footerRowOptions' => ['class' => 'panel panel-info'],
        	'columns' => [
        		['attribute' => 'epic_fdc', 'label' => 'Fecha', 'format' => 'date', 'content' => function ($model) {
        			return Html::a(Yii::$app->formatter->asDate($model->epic_fdc),
        					Url::to(['/clinica/historia/epicrisis/edit', 'id' => $model->epic_id]));
        		}],
        		['attribute' => 'epic_diagnostico', 'label' => 'Diagnóstico',
        				'footer' => Html::beginForm('/clinica/historia/epicrisis/edit', 'post').Camp::dropDownList('epic_recom_id', '', $recomen)
        		],
        		['attribute' => '', 'label' => 'Ver PDF',
        				'content' => function ($model) { return Html::a('PDF',
        						['/clinica/historia/epicrisis/create-pdf', 'id' => $model->epic_id]); },
        				'footer' => Camp::botonSave().Html::endForm()
        		],
        		['attribute' => '', 'label' => 'Cuardar documento PDF',
        				'content' => function ($model) {
        				    return Html::a('Guardar', ['/clinica/historia/epicrisis/create-pdf',
        				            'id' => $model->epic_id, 'guardar' => true]); },
        		],
        	]
        ]);
        return $out;
    }

    public function formEpicrisis ($model)
    {
    	$motivoalta = OpcionClinica::motivoAlta();
    	$out = 	Html::tag('h2', 'Epicrisis del paciente').
    			Html::beginForm('/clinica/historia/epicrisis/edit', 'post').
    			Html::errorSummary($model).
    			"<div class='row'><div class='col-sm-6'>" . "\n".
    			Html::hiddenInput('epic_id', $model->epic_id).
    			Camp::datePicker('epic_fechaingreso', $model->epic_fechaingreso, 'Fecha de ingreso').
    			Camp::datePicker('epic_fechaiq', $model->epic_fechaiq, 'Fecha de intervención').
    			Camp::datePicker('epic_fechaalta', $model->epic_fechaalta, 'Fecha de alta').
    			Camp::textArea('epic_historia', $model->epic_historia, 'Historia').
    			Camp::textArea('epic_antec', $model->epic_antec, 'Antecedentes').
    			"</div><div class='col-sm-6'>" . "\n".
    			Camp::textArea('epic_diagnostico', $model->epic_diagnostico, 'Diagnóstico').
    			Camp::textArea('epic_procedimiento', $model->epic_procedimiento, 'Procedimiento').
    			Camp::textArea('epic_intervencion', $model->epic_intervencion, 'Intervención').
    			Camp::textArea('epic_evolucion', $model->epic_evolucion, 'Evolución').
    			Camp::dropDownList('epic_motivoalta', $model->epic_motivoalta, $motivoalta, 'Motivo del alta').
    			"</div><div class='col-sm-12'>" . "\n".
    			Camp::ckeditor('epic_recom_text', $model->epic_recom_text, 'Recomendaciones', ['height' => '400']).
    			Camp::textArea('epic_notas', $model->epic_notas, 'Observaciones').
    			Camp::botonesNormal('/clinica/historia/epicrisis', $model->epic_id);
    			"</div></div>" . "\n".
    			Html::endForm();

    	return $out;
    }
}
