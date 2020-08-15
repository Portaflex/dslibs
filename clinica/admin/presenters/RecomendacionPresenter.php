<?php

/**
 * Esta es la clase implementa el presentador control de recomendaciones
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use dslibs\clinica\admin\models\RecomendacionModel;
use dslibs\helpers\Lista;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class RecomendacionPresenter extends BaseObject
{
    public function gridRecomAlta ()
    {
        $dataProvider = new ActiveDataProvider(['query' => RecomendacionModel::find()
                        ->where(['recom_tipo' => 1])->orderBy('recom_descrip')]);
        
        $out = GridView::widget([
           'dataProvider' => $dataProvider,
           'caption' => Html::tag('h2', 'Para el alta hospitalaria'),
           'summary' => '',
           'tableOptions' => ['class' => 'table table-default'],
           'columns' => [
                ['attribute' => 'recom_descrip', 'content' => function ($model) {
                   return Html::a($model->recom_descrip, '/clinica/admin/recomendacion/edit?id='.$model->recom_id); },
                ],
           ] ]);
        return $out;
    }
    
    public function gridRecomTto ()
    {
        $dataProvider = new ActiveDataProvider(['query' => RecomendacionModel::find()
                        ->where(['recom_tipo' => 2])->orderBy('recom_descrip')]);
        
        $out = GridView::widget([
           'dataProvider' => $dataProvider,
           'caption' => Html::tag('h2', 'Para los tratamientos'),
           'summary' => '',
           'tableOptions' => ['class' => 'table table-default'],
           'columns' => [
                ['attribute' => 'recom_descrip', 'content' => function ($model) {
                    return Html::a($model->recom_descrip, '/clinica/admin/recomendacion/edit?id='.$model->recom_id); },
                ],
           ] ]);
        return $out;
    }

    public function formRecomendaciones ($model)
    {
    	$tipo = Lista::listaMenu('tipo_recomen');
        $out = 	Html::tag('h2', 'Recomendación').
    			Html::beginForm('/clinica/admin/recomendacion/edit?id='.$model->recom_id, 'post').
    			Html::errorSummary($model).
    			Html::hiddenInput('recom_id', $model->recom_id).
    			Camp::textInput('recom_descrip', $model->recom_descrip, 'Título de la recomendaciones').
    			Camp::dropDownList('recom_tipo', $model->recom_tipo, $tipo, 'Tipo de recomendaciones').
    			Camp::ckeditor('recom_text', $model->recom_text, 'Texto de las recomendaciones', ['height' => '400']).
    			Camp::botonesNormal('/clinica/admin/recomendacion', $model->recom_id);
    			Html::endForm();
    	return $out;
    }
}
