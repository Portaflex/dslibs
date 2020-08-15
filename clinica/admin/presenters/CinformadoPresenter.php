<?php

/**
 * Esta es la clase implementa el presentador control de consentimiento
 * informado de la aplicación clínica.
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
use yii\base\BaseObject;
use dslibs\clinica\admin\models\CinformadoModel;
use dslibs\helpers\Camp;

class CinformadoPresenter extends BaseObject
{
    public function gridCinformado ()
    {
        $cinformado = CinformadoModel::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $cinformado,
        ]);

        $caption = Html::tag('h2', 'Control de consentimientos informados').
        		Html::tag('p', Html::a('Nuevo consentimiento', '/clinica/admin/cinformado/edit'));
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
            'caption' => $caption,
        	'summary' => 'Total de consentimientos: {totalCount}',
        	'tableOptions' => ['class' => 'table table-sm table-hover'],
        	'columns' => [
        		['class' => 'yii\grid\SerialColumn'],
        		['attribute' => 'ci_procedimiento', 'content' => function ($model) {
        			return Html::a($model->ci_procedimiento, '/clinica/admin/cinformado/edit?id='.$model->ci_id); },
        		],
        	]
        ]);
        
        return $out;
    }

    public function formCinformadoEdit ($id = false)
    {
        $model = $id ? CinformadoModel::findOne($id) : new CinformadoModel();
    	$out = 	Html::tag('h2', 'Consentimiento informado para '. $model->ci_procedimiento).
    			Html::beginForm('/clinica/admin/cinformado/edit?id='.$model->ci_id, 'post').
    			Html::hiddenInput('ci_id', $model->ci_id).
    			Camp::textInput('ci_procedimiento', $model->ci_procedimiento).
    			Camp::ckeditor('ci_texto', $model->ci_texto, '', ['height' => '600']).
    			Camp::botonesNormal('/clinica/admin/cinformado', $id);
    			Html::endForm();

    	return $out;
    }
}
