<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use dslibs\clinica\historia\models\VisitaModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\clinica\helpers\OpcionClinica;

class VisitaPresenter extends BaseObject
{
    public function gridVisita ()
    {
        $query = VisitaModel::v_visita(Yii::$app->session['p'], Yii::$app->session['e']);
    	$dataProvider = new ActiveDataProvider(['query' => $query]);

    	$out =	GridView::widget([
    		'dataProvider' => $dataProvider,
    		'tableOptions' => ['class' => 'table table-sm'],
    		'summary' => '',
    	    'caption' => Html::tag('h2', 'Visitas del Paciente').
    	        Html::button('Nueva anotación', ['class' => 'btn btn-outline-secondary btn-xs',
    	                'onClick' => "formulario('visita_form')",
    	                'url' => Url::to('/clinica/historia/visita/form'),
    	                'id' => 'visita_form']),
    		'rowOptions' => ['class' => 'info', 'height' => '30px'],
    	    'showHeader' => FALSE,
    		'columns' => [
    				['content' => function ($model) {
    				  if ($model['consulta_fecha'] == date('Y-m-d'))
    				  {
    				     return Camp::boton(Yii::$app->formatter->asDate($model['consulta_fecha']), '', '',
    				            ['id' => 'fecha',
    				             'onClick' => "formulario('fecha')",
    				             'url' => '/clinica/historia/visita/form',
    				             'class' => 'btn btn-outline-primary btn-xs'
    				            ]);
    				  }
    				  else return "<b>".Yii::$app->formatter->asDate($model['consulta_fecha'])."</b>"; }
    				],
    				['content' => function ($model) {
    					return $model['tipo_consulta']; }
    				],
    				['content' => function ($model) {
    					return Html::tag('b', $model['sanitario']); }
    				],
    				['content' => function ($model) {
    					return Html::hiddenInput('consulta_id', $model['consulta_id']); }
    				],
    		],
    		'afterRow' =>  function ($model) {
    			return "<tr><td colspan=3><p>".$model['consulta_notas']."</p></td></tr>"; }
    	]);
    	return $out;
    }
    
    public function formVisita ($id = false)
    {
        $consultaTipo = OpcionClinica::tipoCita();
        $model = $id ? VisitaModel::findOne($id) : new VisitaModel();
        $out = Html::beginForm('/clinica/historia/visita', 'post').
        Html::hiddenInput('consulta_id', $model->consulta_id).
        Camp::dropDownList('consulta_tipo', $model->consulta_tipo , $consultaTipo, 'Tipo de consulta').
        Camp::ckeditor('consulta_notas', $model->consulta_notas).
        Camp::botonesNormal('/clinica/historia/visita', $id).
        Html::endForm();
        return $out;
    }
}
