<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use dslibs\clinica\historia\models\PacienteAntecModel;
use dslibs\helpers\Lista;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class AntecedentePresenter extends BaseObject
{
    public function gridAntec ()
    {
        $antecedentes = Lista::lista('antecedente', 'antec_id', 'antec_desc', '', true, 'antec_desc');
    	
    	$dataProvider = new ActiveDataProvider([
    	    'query' => PacienteAntecModel::find()
    	               ->where(['pa_pac_id' => Yii::$app->session['p']])
    	               ->joinWith(['antec a'])
    	]);
    	$dataProvider->sort->attributes['antecedente'] = [
    	    'asc' => ['a.antec_desc' => SORT_ASC],
    	    'desc' => ['a.antec_desc' => SORT_DESC]
    	];
    	
    	$out = GridView::widget([
    		'dataProvider' => $dataProvider,
    	    'caption' => Html::tag('h2', 'Antecedentes del Paciente'),
    	    'summary' => '',
    		'tableOptions' => ['class' => 'table table-sm'],
    		'showFooter' => true,
    		'footerRowOptions' => ['class' => 'panel panel-info'],
    		'columns' => [
    			['attribute' => 'pa_fdc', 'format' => 'date', 'label' => 'Fecha'],
    			['attribute' => 'antecedente', 'value' => 'antec.antec_desc'],
    			['attribute' => 'pa_txt', 'content' => function ($model) {
    				return Camp::textArea('pa_txt', $model->pa_txt).Html::hiddenInput('pa_id', $model->pa_id); },
    			 'footer' => Camp::dropDownList('pa_antec_id', '', $antecedentes)
    			],
    			['content' => function ($model) {
    				return Camp::botonesAjax('/clinica/historia/antecedente/edit', 'actualiza'); },
    			 'footer' => Camp::botonAjax('Nuevo', 'actualiza', '/clinica/historia/antecedente/edit',
    			     ['class' => 'light'])]
    		],
    	]);
    	return $out;
    }
}
