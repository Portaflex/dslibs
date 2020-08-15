<?php

/**
 * Esta clase presenta los datos dinámicos del control de recursos de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\presenters;

use Yii;
use yii\grid\GridView;
use dslibs\helpers\Camp;
use dslibs\helpers\Lista;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\base\BaseObject;
use dslibs\admin\models\RecursoModel;

class RecursoPresenter extends BaseObject
{
    public static function editRecurso ()
    {
    	if ($data = Yii::$app->request->post())
    	{
    		if (isset($data['action']) && $data['action'] == 'save')
    		{
    			if ($data['r_id'] == '')
    			{
    			    $model = new RecursoModel();
    			    unset($data['r_id']);
    			}
    			else
    			{
    			    $model = RecursoModel::findOne($data['r_id']);
    			}
    			
    			$model->attributes = $data;
    			if ($model->save()) return $model->r_id;

    		}
    		if (isset($data['action']) && $data['action'] == 'delete')
    		{
    		    $model = RecursoModel::findOne($data['r_id']);
    			if ($model->delete()) return 'delete' ;
    		}
    	}
    }

    public static function editRol  ()
    {
    	if ($data = Yii::$app->request->post())
    	{
    		if ($data['action'] == 'save')
    		{
    			if (isset($data['rr_id']))
    			{
    			    Yii::$app->db->createCommand()->update('recurso_rol', [
    			        'rol_id' => $data['rol_id']], ['rr_id' => $data['rr_id']])
    			    ->execute();
    			}
    			else 
    			{
    			    Yii::$app->db->createCommand()->insert('recurso_rol', [
    			        'ruta_id' => $data['ruta_id'], 'rol_id' => $data['rol_id']])
    			    ->execute();
    			}

    		}
    		if ($data['action'] == 'delete')
    		{
    			Yii::$app->db->createCommand()->delete('recurso_rol', ['rr_id' => $data['rr_id']])
    			->execute();
    		}
    		return self::gridPermiso($data['ruta_id']);
    	}
    }

    public static function gridRecurso ()
    {
        $searchModel = new RecursoModel();
        $dataProvider = $searchModel->search();
        $dataProvider->pagination->pageParam = 'rec-page';
        $dataProvider->sort->sortParam = 'rec-sort';
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	'filterModel' => $searchModel,
        	'tableOptions' => ['class' => 'table table-sm'],
        	'caption' => Html::tag('h2', Html::a('Permisos de recursoss', '/admin/recurso')),
        	'summary' => '',
        	'columns' => [
        		['attribute' => 'r_ruta', 'label' => 'Recurso', 'content' => function ($model) {
        			return Html::tag('b', Html::a($model['r_ruta'], ['/admin/recurso/edit', 'id' => $model['r_id']])); },
        		 'filter' => Camp::textInput('r_ruta', Yii::$app->session['ruta'])
        		]
        	],
        ]);
    	return $out;
    }

    public static function formRecurso ($id = false)
    {
        $model = $id ? RecursoModel::findOne($id) : new RecursoModel();
    	$out = Html::tag('h2', 'Permisos').
    	Html::beginForm('/admin/recurso/edit-recurso', 'post').
    	html::hiddenInput('r_id', $model['r_id']).
    	Camp::textInput('r_ruta', $model['r_ruta'], 'Recurso').
    	Camp::botonesNormal('/admin/recurso', $id).
    	Html::endForm();
    	return $out;
    }

    public static function gridPermiso ($r_id = false)
    {
    	$query = (new Query())->from('recurso_rol')->where(['ruta_id' => $r_id]);
    	//$query = RecursoPresenter::findOne($r_id)->getRecursoRol();
    	$rperProvider = new ActiveDataProvider([
    		'query' => $query,
    		'pagination' => ['pageParam' => 'rper-page'],
    		'sort' => ['sortParam' => 'rper-sort']
    	]);
    	$rol_id = self::usuarioRol();
    	$out = GridView::widget([
    		'dataProvider' => $rperProvider,
        	'tableOptions' => ['class' => 'table table-default'],
    		'summary' => '',
    		'showFooter' => true,
    		'columns' => [
    			['attribute' => 'rol_id', 'label' => 'Roles autorizados', 'content' => function ($model) use ($rol_id) {
    				return 	Camp::dropDownList('rol_id', $model['rol_id'], $rol_id).
    						Html::hiddenInput('ruta_id', $model['ruta_id']).
    						Html::hiddenInput('rr_id', $model['rr_id']); },
    			 'footer' => Camp::dropDownList('rol_id', '', $rol_id, '', ['onChange' => "actualiza('nuevo')",
    			             'id' => 'nuevo', 'url' => '/admin/recurso/edit-rol', 'action' => 'save']).
    						Html::hiddenInput('ruta_id', $r_id)
    			],
        		['attribute' => null, 'content' => function ($model, $key, $index) {
        			return Camp::botonesAjax('/admin/recurso/edit-rol', 'actualiza'); },
        		// 'footer' => Camp::botonAjax('Nuevo rol', "actualiza_tabla_recarga",
        		//			'/admin/recursos/edit-rol', ['class' => 'info', 'action' => 'save'])
        		],
    		]
    	]);
    	return $out;
    }
    
    private function usuarioRol()
    {
        return Lista::lista('menu', 'm_valor', 'm_texto', ['m_ident' => 'usuario_rol'], true, 'm_valor');
    }
}

// Final del documento.
