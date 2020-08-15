<?php

namespace dslibs\admin\helpers;

use dslibs\clinica\admin\models\LogaccesoModel;
use Yii;
use yii\base\Behavior;
use yii\bootstrap4\Nav;
use yii\db\Query;

class InformaAdmin extends Behavior
{	
	public function menuPrincipal ()
	{
	    if (isset(Yii::$app->session['userRol']))
	    {
	        $items = (new Query())->select(['m.m_url', 'm.m_texto', 'mr.mr_rol_id'])
    	        ->where(['mr.mr_rol_id' => Yii::$app->session['userRol'], 'm_ident' => 'menu_principal'])
    	        ->from('menu_rol mr')->orderBy('m_orden')
    	        ->leftJoin('menu m', 'mr.mr_menu_id = m.m_id')->all();
	        
    	    $item = [];
	        
    	    foreach ($items as $i)
    	    {
    	        $item[] = [
    	                'label' => $i['m_texto'],
    	                'url' => $i['m_url'],
    	        ];
    	    }
    	    $out = Nav::widget([
    	            'items' => $item,
    	            'options' => ['class' => 'navbar-nav mr-auto']
    	    ]);
    	    
    	    return $out;
	    }
	}
	
	public function menuAdminCms ()
	{
	    if (Yii::$app->session['userRol'] == 1)
	    {
	        $items = (new Query())->select(['m.m_url', 'm.m_texto', 'mr.mr_rol_id'])
	        ->where(['mr.mr_rol_id' => Yii::$app->session['userRol'], 'm_ident' => 'admin_cms'])
	        ->from('menu_rol mr')->orderBy('m_texto')
	        ->leftJoin('menu m', 'mr.mr_menu_id = m.m_id')->all();
	        
	        $item = [];
	        
	        foreach ($items as $i)
	        {
	            $item[] = [
	                'label' => $i['m_texto'],
	                'url' => $i['m_url'],
	            ];
	        }
	        
	        $out = "<div class='card'>" . "\n";
	        $out .= "<div class='card-header'> Admin CMS </div>" . "\n";
	        $out .= Nav::widget([
	            'items' => $item,
	            'options' => ['class' => 'nav flex-column']
	        ]);
	        $out .= "</div><br>" . "\n";
	        
	        return $out;
	    }
	}
	
	public static function logDatabase()
	{
		$data['la_pagina'] = Yii::$app->request->userIP . ' -- ' . Yii::$app->request->url;
		if (isset(Yii::$app->session['userLogin'])) $data['la_userlogin'] = Yii::$app->session['userLogin'];
		$model = new LogaccesoModel();
		$model->attributes = $data;
		$model->save();
	}
}