<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use dslibs\clinica\historia\models\ImagenModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class ImagenPresenter extends BaseObject
{
    public function gridImagen ()
    {
    	$dataProvider = new ActiveDataProvider([
    	    'query' => ImagenModel::find()->where(['image_epis_id' => Yii::$app->session['e']])
    	]);
    	$out = GridView::widget([
    			'dataProvider' => $dataProvider,
    			//'filterModel' => $searchModel,
    			'summary' => '',
    	        'caption' => Html::tag('h2', 'Imágenes del Paciente'),
    			'tableOptions' => ['class' => 'table table-sm'],
    			//'showFooter' => true,
    			'footerRowOptions' => ['class' => 'panel panel-info'],
    			'columns' => [
    					'image_nombre',
    					['attribute' => 'image_imagen', 'content' => function ($model) {
    						return Html::a(Html::img('@web/imagenes/'.$model->image_imagen,
    								['height' => 200, 'width' => 200, 'alt' => $model->image_imagen]),
    								['/clinica/historia/imagen/descarga', 'id' => $model->image_id]).
    								Html::hiddenInput('image_id', $model->image_id);
    					}],
    					//'image_userlogin',
    					['attribute' => 'image_fdc', 'format' => 'date'],
    					['attribute' => '', 'content' => function ($model) {
    						return Camp::botonAjax('Del', 'actualiza', '/clinica/historia/imagen/edit', ['class' => 'danger',
    								'action' => 'delete']);
    					}]
    			]
    	]);
    	return $out;
    }
    
    public function subirImagenForm ()
    {
        $out = Html::tag('h4', 'Nueva Imagen').
        Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']).
        Camp::textInput('image_nombre').
        Camp::fileInput('image_imagen', '', 'Seleccionar', ['class' => 'file']).
        Camp::botonSend().
        Html::endForm();
        
        return $out;
    }
}
