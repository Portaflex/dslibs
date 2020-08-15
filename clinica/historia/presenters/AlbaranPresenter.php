<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use dslibs\clinica\admin\models\AlbaranModel;
use dslibs\helpers\Lista;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class AlbaranPresenter extends BaseObject
{
    public function gridAlbaran($footer = FALSE)
    {
        $acto = Lista::listaMenu('tipo_albaran');
        $cobro = Lista::listaSimple('estado_cobro');
        $pago = Lista::listaSimple('estado_pago');
        $facturado = Lista::listaSimple('booleano');
    	
    	$dataProvider = new ActiveDataProvider([
    	    'query' => AlbaranModel::v_albaran()->where(['a_epis_id' => Yii::$app->session['e']]),
    	    'sort' => [
    	        'attributes' => [
    	            'a_fecha_acto' => ['asc' => ['a_fecha_acto' => SORT_ASC], 'desc' => ['a_fecha_acto' => SORT_DESC]],
    	            'a_acto' => ['asc' => ['a_acto' => SORT_ASC], 'desc' => ['a_acto' => SORT_DESC]],
    	            'sanitario' => ['asc' => ['sanitario' => SORT_ASC], 'desc' => ['sanitario' => SORT_DESC]],
    	            'a_estado' => ['asc' => ['a_estado' => SORT_ASC], 'desc' => ['a_estado' => SORT_DESC]],
    	            'a_pago' => ['asc' => ['a_pago' => SORT_ASC], 'desc' => ['a_pago' => SORT_DESC]],
    	            'a_precio' => ['asc' => ['a_precio' => SORT_ASC], 'desc' => ['a_precio' => SORT_DESC]],
    	            'a_transaccion' => ['asc' => ['a_transaccion' => SORT_ASC], 'desc' => ['a_transaccion' => SORT_DESC]],
    	            'a_facturado'
    	        ],
    	        'defaultOrder' => ['a_fecha_acto' => SORT_DESC]
    	    ]
    	]);

    	$out = GridView::widget([
    			'dataProvider' => $dataProvider,
    			'tableOptions' => ['class' => 'table table-sm'],
    	        'id' => 'albaranes',
    	        'layout' => "{items}\n{summary}\n{pager}",
    	        'caption' => Html::tag('h2', 'Albaranes del paciente'),
    			'summary' => 'Total de albaranes: {totalCount}',
    			'showFooter' => $footer,
    			'footerRowOptions' => ['class' => 'panel panel-info'],
    			'columns' => [
    				['attribute' => 'a_fecha_acto', 'label' => 'Fecha', 'content' => function ($model) {
    				    
    				    if ($model['a_cita_id'] != NULL) $fecha = html::a(Yii::$app->formatter->asDate($model['a_fecha_acto']),
    				        ['/clinica/historia/albaran/index', 'cita_id' => $model['a_cita_id']]);
    				    if ($model['a_iq_id'] != NULL) $fecha = html::a(Yii::$app->formatter->asDate($model['a_fecha_acto']),
    				        ['/clinica/historia/albaran/index', 'iq_id' => $model['a_iq_id']]);
    				    
    					return $fecha . Html::hiddenInput('a_id', $model['a_id']); },
    					'footer' => Camp::datePicker('a_fecha_acto', '', '', ['style'=>'width:80px;'])
    					
    				],
    				['attribute' => 'a_acto', 'label' => 'Acto',  'content' => function ($model) use ($acto) {
    					return Camp::dropDownList('a_acto', $model['a_acto'], $acto); },
    					'footer' => Camp::dropDownList('a_acto', '', $acto)
    				],
    				['attribute' => 'a_transaccion', 'label' => 'Transacción', 'content' => function ($model) {
    				    return Camp::textInput('a_transaccion', $model['a_transaccion'], '', ['style' => 'width:100px']); },
    				    'footer' => Camp::textInput('a_transaccion', '', '', ['style' => 'width:100px'])
    				],
    				'sanitario',
    				['attribute' => 'a_facturado', 'label' => 'Facturado',  'content' => function ($model) use ($facturado) {
    					return Camp::dropDownList('a_facturado', $model['a_facturado'], $facturado); },
    					'footer' => Camp::dropDownList('a_facturado', 0, $facturado)
    				],
    				['attribute' => 'a_estado', 'label' => 'Cobro',  'content' => function ($model) use ($cobro) {
    					return Camp::dropDownList('a_estado', $model['a_estado'], $cobro, '', ['class' => 'form-control input-sm '.
    							$model['estado']]); },
    					'footer' => Camp::dropDownList('a_estado', 0, $cobro)
    				],
    				['attribute' => 'a_pago', 'label' => 'Pago',  'content' => function ($model) use ($pago) {
    					return Camp::dropDownList('a_pago', $model['a_pago'], $pago, '', ['class' => 'form-control input-sm '.
    							$model['pago']]); },
    					'footer' => Camp::dropDownList('a_pago', 0, $pago)
    				],
    				['attribute' => 'a_precio', 'label' => 'Precio',  'label' => 'Precio(€)', 'content' => function ($model) {
    				    return Camp::textInput('a_precio', $model['a_precio'], '', ['style'=>'width:70px; text-align:right;']); },
    				    'footer' => Camp::textInput('a_precio', '', '', ['style' => 'width:70px'])
    				],
    				['attribute' => null, 'content' => function($model, $key, $index){
    				    return Camp::botonesAjax('/clinica/historia/albaran/edit-albaran', 'actualiza'); },
    					'footer' => Camp::botonAjax('Nuevo', 'actualiza', '/clinica/historia/albaran/edit-albaran',
    					        ['class' => 'light', 'action' => 'save'])
	    			]
    			],
    	]);
    	return $out;
    }
}

// Final del documento.
