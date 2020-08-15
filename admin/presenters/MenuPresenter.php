<?php

/**
 * Esta clase presenta los datos dinámicos del control de menus de la aplicación
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
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\base\BaseObject;
use yii\bootstrap4\Html;
use dslibs\helpers\Lista;
use dslibs\helpers\Camp;

class MenuPresenter extends BaseObject
{
    public static function permiso ()
    {
    	if ($data = Yii::$app->request->post())
    	{
    		if ($data['action'] == 'save')
    		{
    			Yii::$app->db->createCommand()->insert('menu_rol',
    				['mr_menu_id' => $data['mr_menu_id'], 'mr_rol_id' => $data['mr_rol_id']])->execute();
    		}
    		if ($data['action'] == 'delete')
    		{
    			Yii::$app->db->createCommand()->delete('menu_rol', ['mr_id' => $data['mr_id']])->execute();
    		}
    	}
    }

    public static function formMenu ($model)
    {
    	//if ($parent) $model->m_parent = $parent;
        $out = Html::tag('h2', 'Propiedades de Menú').
    	Html::beginForm('/admin/menu/edit', 'post').
    	Camp::textInput('m_id', $model->m_id, 'ID').
    	Camp::textInput('m_parent', $model->m_parent, 'Parent').
    	Camp::textInput('m_valor', $model->m_valor, 'Valor').
    	Camp::textInput('m_ident', $model->m_ident, 'Ident').
    	Camp::textInput('m_grupo', $model->m_grupo, 'Grupo').
    	Camp::textInput('m_orden', $model->m_orden, 'Orden').
    	Camp::textInput('m_texto', $model->m_texto, 'Texto').
    	Camp::textInput('m_url', $model->m_url, 'Url').
    	Camp::textInput('m_curl', $model->m_curl, 'CUrl'). "<br>".
    	Camp::botonesNormal('/admin/menu', $model->m_id).
    	Html::endForm();
    	return $out;
    }

    public static function gridMenuPermiso ($id)
    {
    	$rol = self::usuarioRol();
    	$query = (new Query())->select(['mr.*', 'm.m_texto'])->from('menu_rol mr')
    				->leftJoin('menu m', "mr.mr_rol_id = m.m_valor and m.m_ident = 'usuario_rol'")->where(['mr.mr_menu_id' => $id]);

    	$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    	]);
    	
    	$out = GridView::widget([
    		'dataProvider' => $dataProvider,
    		'summary' => '',
    		'caption' => Html::tag('h2', 'Permisos del Menú'),
    		'showFooter' => true,
    		'columns' => [
    			['attribute' => 'm_texto', 'label' => 'Roles autorizados',
    			 'footer' => Camp::dropDownList('mr_rol_id', '', $rol).
    						 Html::hiddenInput('mr_menu_id', $id)
    			],
    			['attribute' => '', 'content' => function ($model, $key, $ident) {
    				return 	Html::hiddenInput('mr_id', $model['mr_id']).
    				Camp::botonAjax('Borrar', 'actualiza', '/admin/menu/permiso', ['class' => 'danger', 'action' => 'delete']); },
    			 'footer' => Camp::botonAjax('Nuevo', 'actualiza',
    							'/admin/menu/permiso', ['class' => 'primary', 'action' => 'save'])
    			],
    		],
    	]);
    	return $out;
    }
    
    private function usuarioRol()
    {
        return Lista::lista('menu', 'm_valor', 'm_texto', ['m_ident' => 'usuario_rol'], true, 'm_valor');
    }
}
