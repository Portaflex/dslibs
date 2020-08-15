<?php

/**
 * Esta es la clase implementa el presentador control de pacientes
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use Yii;
use yii\grid\GridView;
use yii\helpers\Html;
use dslibs\clinica\admin\models\PacienteSearch;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class PacientePresenter extends BaseObject
{
    public function gridPaciente()
    {
    	$searchModel = new PacienteSearch();
    	$dataprovider = $searchModel->search(Yii::$app->request->queryParams);

    	$out = 	Html::tag('h2', 'Listado de Pacientes').
    			GridView::widget([
	    			'dataProvider' => $dataprovider,
    				'filterModel' => $searchModel,
    				'tableOptions' => ['class' => 'table'],
	    			'showFooter' => True,
    				'footerRowOptions' => ['class' => 'panel panel-default'],
    				'summary' => 'Total de Pacientes: {totalCount}',
	    			'columns' => [
	    					//['class' => 'yii\grid\SerialColumn'],
	    					['attribute' => 'pac_id',
	    					    'content' => function ($model) {
	    					    return Html::a($model->pac_id, ['/clinica/historia/episodio/', 'p' => $model->pac_id]);
	    					    },
	    					    'footer' => Html::tag('b', 'Nuevo')
	    					],
	    					[	'attribute' => 'pac_nom',
	    						'footer' => Html::beginForm('paciente/insert', 'post') . Camp::textInput('pac_nom')
	    					],
	    					[	'attribute' => 'pac_apell1',
	    						'footer' => Camp::textInput('pac_apell1')
	    					],
	    					[	'attribute' => 'pac_apell2',
	    						'footer' => Camp::textInput('pac_apell2')
	    					],
	    					[	'attribute' => 'pac_fnac', 'format' => ['date'],
	    						'footer' => Camp::datePicker('pac_fnac')
	    					],
	    					[	'attribute' => 'pac_telefo',
	    						'footer' => Camp::textInput('pac_telefo')
	    					],
	    					[	'attribute' => 'pac_telefo2',
	    						'footer' => Camp::textInput('pac_telefo2')
	    					],
	    					[	'attribute' => 'pac_email',
	    						'footer' => Camp::textInput('pac_email')
	    					],
	    					[	'attribute' => null,
	    						'footer' => Camp::botonSave() . Html::endForm()
	    					]
	    			],
    			]);
    	return $out;
    }
}
