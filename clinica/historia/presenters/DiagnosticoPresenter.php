<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use dslibs\clinica\historia\models\EpisodioDxModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\clinica\helpers\OpcionClinica;

class DiagnosticoPresenter extends BaseObject
{
	public function gridDiagnostico ($sub = false)
	{
		$zona_dx = OpcionClinica::diagnostico();
		if ($sub) $sub_dx = OpcionClinica::diagnosticoSub($sub);
		$sub_dropdown = $sub ? Camp::dropDownList('edx_subdx_id', '', $sub_dx, 'Diagnóstico') : '';

		$query = EpisodioDxModel::find()
		  ->joinWith(['dx d', 'subDx sd'])
		  ->where(['edx_epis_id' => Yii::$app->session['e']])->with(['dx', 'subDx']);
		
		$dataProvider = new ActiveDataProvider([
		    'query' => $query,
		    'sort' => [
		        'attributes' => [
		            'edx_dx_id', 'edx_txt'
		        ]
		    ]
		]);

		$out = GridView::widget([
				'dataProvider' => $dataProvider,
		        'caption' => Html::tag('h2', 'Diagnósticos del Paciente'),
				'summary' => '',
				'tableOptions' => ['class' => 'table table-sm'],
				'showFooter' => true,
				'footerRowOptions' => ['class' => 'panel panel-info'],
				'columns' => [
						['attribute' => 'edx_dx_id', 'content' => function ($model) {
							return 	Html::tag('h4', Html::ul([$model->dx['dx_descripcion'],
									$model->subDx['dx_descripcion']]));
						}],
						//['attribute' => 'antecedente', 'value' => 'antec.antec_desc'],
						['attribute' => 'edx_txt', 'label' => 'Descripción', 'content' => function ($model) {
							return Camp::textArea('edx_txt', $model->edx_txt).Html::hiddenInput('edx_id', $model->edx_id); },
							'footer' => Camp::dropDownList('edx_dx_id', $sub, $zona_dx, 'Zona y diagnóstico', [
									'id' => 'menu', 'onChange' => "sub_menu('menu')",
									'url' => '/clinica/historia/diagnostico/sub-zona']) . '<br>' . $sub_dropdown
						],
						['content' => function ($model) {
							return Camp::botonesAjax('/clinica/historia/diagnostico/edit', 'actualiza'); },
							'footer' => "<br>" . Camp::botonAjax('Nuevo', 'actualiza', '/clinica/historia/diagnostico/edit',
							        ['class' => 'light'])
						]
				],
			]);
		return $out;
	}
}
