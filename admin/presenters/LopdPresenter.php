<?php

/**
 * Esta clase presenta los datos dinámicos de la LOPD de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\presenters;

use dslibs\admin\models\LopdModel;
use dslibs\helpers\Camp;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\base\BaseObject;

class LopdPresenter extends BaseObject
{
    public static function gridLopd ()
    {
    	$lopd = LopdModel::find();
    	$dataProvider = new ActiveDataProvider([
    		'query' => $lopd,
    		'pagination' => [
    			'pageSize' => 100
    		],
    	    'sort' => [
    	        'defaultOrder' => ['pd_ident' => SORT_ASC]
    	    ]
    	]);

    	$out = Html::tag('h2', 'Control de documentos LOPD');
    	$out .= GridView::widget([
    	    'dataProvider' => $dataProvider,
    	    'tableOptions' => ['class' => 'table table-sm'],
	        'columns' => [
	            ['content' => function ($model) {
	                return Html::tag('h4', Html::a($model->pd_ident, ['/admin/lopd/edit', 'id' => $model->pd_id]));
	            },
	            'header' => Html::a('Nuevo documento', '/admin/lopd/edit', ['class' => 'boton'])],
	            ['content' => function ($model) {
	                return Html::label($model->publico['m_texto']);
	            }, 'header' => 'Visible'],
	            ['content' => function ($model) {
	                return Html::tag('h4', Html::a('PDF', ['/admin/lopd/pdf', 'id' => $model->pd_id]));
	            }],
    	    ]
    	]);
    	return $out;
    }
    
    public static function gridLopdInforma ()
    {
        $lopd = LopdModel::find()->where(['pd_publico' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $lopd,
            'pagination' => [
                'pageSize' => 100
            ],
            'sort' => [
                'defaultOrder' => ['pd_ident' => SORT_ASC]
            ]
        ]);
        
        $out = Html::tag('h2', 'Documentos informativos de la LOPD');
        $out .= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                ['content' => function ($model) {
                    return Html::tag('h3', Html::a($model->pd_ident, ['/lopd/pdf', 'id' => $model->pd_id])).
                        '<hr>'.$model->pd_contenido;
                }]]]);
        return $out;
    }
    
    public static function formLopdEdit ($id = false)
    {
        $model = $id ? LopdModel::findOne($id) : new LopdModel();
        $booleano = [1 => 'Si', 0 => 'No'];
        
        $out = 	Html::tag('h2', 'Documento de la LOPD').
        Html::beginForm('/admin/lopd/edit?id='.$model->pd_id, 'post').
        Html::hiddenInput('pd_id', $model->pd_id).
        Camp::textInput('pd_ident', $model->pd_ident).
        Camp::ckeditor('pd_contenido', $model->pd_contenido, '', ['preset' => 'DS']).
        Camp::dropDownList('pd_publico',$model->pd_publico, $booleano, 'Publico: ').
        Camp::botonesNormal('/admin/lopd', $id);
        Html::endForm();
        
        return $out;
    }
}
