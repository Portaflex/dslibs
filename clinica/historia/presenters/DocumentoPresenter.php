<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use dslibs\clinica\historia\models\DocsPacienteModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class DocumentoPresenter extends BaseObject
{
    public  function gridDocumento ()
    {
    	$out = GridView::widget([
    			'dataProvider' => new ActiveDataProvider([
    			    'query' => DocsPacienteModel::find()->where(['doc_epis_id' => Yii::$app->session['e']])
    			]),
    			//'filterModel' => $searchModel,
    			'summary' => '',
    	        'caption' => Html::tag('h2', 'Documentos del Paciente'),
    			'tableOptions' => ['class' => 'table table-sm'],
    			//'showFooter' => true,
    			'footerRowOptions' => ['class' => 'panel panel-info'],
    			'columns' => [
    					'doc_titulo',
    					['attribute' => 'doc_nombre', 'label' => 'Nombre', 'content' => function ($model) {
    						return Html::a($model->doc_nombre, ['/clinica/historia/documento/descarga',
    						        'id' => $model->doc_id]) . Html::hiddenInput('doc_id', $model->doc_id);
    					}],
    					'doc_userlogin',
    					['attribute' => 'doc_fdc', 'format' => 'date'],
    					['attribute' => '', 'content' => function ($model) {
    						return Camp::botonAjax('Del', 'actualiza', '/clinica/historia/documento/edit',
    						    ['class' => 'danger', 'action' => 'delete']);
    					}]
    			]
    	]);
    	return $out;
    }
    
    public function subirDocumentoForm ()
    {
        $out = Html::tag('h4', 'Nuevo documento').
        Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']).
        Camp::textInput('doc_titulo').
        Camp::fileInput('doc_nombre', '', 'Seleccionar', ['class' => 'file']).
        Camp::botonSend().
        Html::endForm();
        
        return $out;
    }
}
