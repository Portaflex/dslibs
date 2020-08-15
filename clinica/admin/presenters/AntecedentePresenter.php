<?php

/**
 * Esta es la clase implementa el presentador control de antecedentes del paciente
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use yii\data\ActiveDataProvider;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use dslibs\clinica\admin\models\AntecedenteModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class AntecedentePresenter extends BaseObject
{
    public function gridAntecedente ()
    {
    	$dataProvider = new ActiveDataProvider([
    	    'query' => AntecedenteModel::find(),
    	    'pagination' => [ 'pageSize' => 30 ]
    	]);

    	$caption = Html::tag('h2', 'Gestión de Antecedentes');
    	$out = GridView::widget([
    		'dataProvider' => $dataProvider,
    	    'caption' => $caption,
    		'tableOptions' => ['class' => 'table table-sm'],
    		'summary' => '',
    		'showFooter' => true,
    		'columns' => [
    		    ['class' => 'yii\grid\SerialColumn'],
    			['attribute' => 'antec_desc', 'label' => 'Antecedente', 'content' => function ($model) {
    			    return Camp::textInput('antec_desc', $model->antec_desc); },
    			 'footer' => Camp::textInput('antec_desc')
    			],
    			['attribute' => '', 'content' => function ($model) {
    				return  Camp::botonesAjax('/clinica/admin/antecedente/edit', 'actualiza').
    				        Html::hiddenInput('antec_id', $model->antec_id); },
    			 'footer' => Camp::botonAjax('Nuevo', 'actualiza_recarga', '/clinica/admin/antecedente/edit',
    						['class' => 'info', 'action' => 'save' ])
    			]
    		],
    	]);
    	return $out;
    }
}

//Final del documento.
