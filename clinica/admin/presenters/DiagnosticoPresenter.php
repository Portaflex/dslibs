<?php

/**
 * Esta es la clase implementa el presentador control de diagnósticos
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
use dslibs\clinica\admin\models\DiagnosticoModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class DiagnosticoPresenter extends BaseObject
{
    public static  function arbolDiagnostico ()
    {
    	$params = [
    			'tabla' => 'dx',
    			'id' => 'dx_id',
    			'parent' => 'dx_parent',
    			'ref' => '/clinica//admin/diagnostico',
    			'ref_insert' => '/clinica/admin/diagnostico/insert',
    			'ref_edit' => '/clinica/admin/diagnostico/edit',
    			'nombre' => 'dx_descripcion',
    			'titulo' => 'Gestor de Diagnósticos',
    			'nuevo' => 'Nuevo diagnóstico raíz',
    			//'dropdown' => 'm_ident',
    			'id_link' => TRUE,
    	];
    	return $params;
    }

    public static  function formDiagnostico ($id = false, $parent = false)
    {
        $model = $id ? DiagnosticoModel::findOne($id) : new DiagnosticoModel();
    	if ($parent) $model->dx_parent = $parent;
    	$out = Html::tag('h2', 'Dianóstico').
    	Html::beginForm('/clinica/admin/diagnostico/edit', 'post').
    	Html::hiddenInput('dx_id', $model->dx_id).
    	Camp::textInput('dx_parent', $model->dx_parent, 'Parent').
    	Camp::textInput('dx_descripcion', $model->dx_descripcion, 'Descripción').
    	Camp::botonesNormal('/clinica/admin/diagnostico', $id).
    	Html::endForm();
    	return $out;
    }
}
