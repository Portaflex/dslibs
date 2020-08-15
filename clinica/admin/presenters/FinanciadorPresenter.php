<?php

/**
 * Esta es la clase implementa el presentador control de financiadores
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use dslibs\clinica\admin\models\FinanciadorModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\clinica\helpers\OpcionClinica;

class FinanciadorPresenter extends BaseObject
{
    public function gridFinanciador ()
    {
        $financiador = FinanciadorModel::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $financiador
        ]);

        $caption = Html::tag('h2', 'Control de financiadores') . Html::a('Nuevo financiador', '/clinica/admin/financiador/edit');
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	'caption' => $caption,
        	'summary' => '<br>',
        	'columns' => [
        		['attribute' => 'finan_empresa', 'content' => function ($model) {
        			return Html::a($model['finan_empresa'], '/clinica/admin/financiador/edit?id='.$model['finan_id']); }
        		],
        		['attribute' => 'finan_membrete', 'content' => function ($model) {
        			return Html::label($model['finan_membrete']); }
        		], 'finan_activo'
        	]
        ]);
        return $out;
    }

    public function formFinanciador ($id = null)
    {
    	$activo = OpcionClinica::booleano();
        $finan = isset($id) ? FinanciadorModel::findOne($id) : new FinanciadorModel();

    	$out = Html::tag('h2', 'Datos del financiador').
    		Html::beginForm('/clinica/admin/financiador/edit', 'post').
    		Camp::textInput('finan_empresa', $finan->finan_empresa, 'Empresa').
    		Html::hiddenInput('finan_id', $finan->finan_id).
    		Camp::textInput('finan_gestor', $finan->finan_gestor, 'Gestor').
    		Camp::textInput('finan_telefono', $finan->finan_telefono, 'Teléfono').
    		Camp::ckeditor('finan_membrete', $finan->finan_membrete, 'Membrete').
    		Camp::dropDownList('finan_activo', $finan->finan_activo, $activo, 'Activo').
    		Camp::botonesNormal('/clinica/admin/financiador', $id).
    		Html::endForm();
    	return $out;
    }
}
