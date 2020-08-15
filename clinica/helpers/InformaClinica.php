<?php

namespace dslibs\clinica\helpers;

use dslibs\clinica\admin\models\LogaccesoModel;
use dslibs\clinica\admin\models\PacienteModel;
use Yii;
use yii\base\Behavior;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use dslibs\clinica\historia\models\EpisodioModel;

class InformaClinica extends Behavior
{
    public static function pacienteActivo ()
	{
		if (isset(Yii::$app->session['e']))
		{
			$model = EpisodioModel::v_episodioPaciente()->where(['epis_id' => Yii::$app->session['e']])->one();
			Yii::$app->session['paciente'] = $model['paciente'];
			Yii::$app->session['expediente'] = $model['epis_expediente'];
			$out = Html::tag('i', html::a($model['paciente'], '/clinica/historia/visita/'.
			    $model['epis_id'].'/'. $model['pac_id']).' ('. $model['edad'].' años) <b>'.$model['financiador'].'</b> - '.$model['departamento']);
			return $out;
		}
	}
	
	public static function menuAdminClinica ()
	{
	    if (isset(Yii::$app->session['userRol']))
	    {
	        $items = (new Query())->select(['m.m_url', 'm.m_texto', 'mr.mr_rol_id'])
	            ->where(['mr.mr_rol_id' => Yii::$app->session['userRol'], 'm_ident' => 'admin_clinica'])
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
            $out .= "<div class='card-header'> Admin Clínica </div>" . "\n";
            $out .= Nav::widget([
                'items' => $item,
                'options' => ['class' => 'nav flex-column']
            ]);
            $out .= "</div><br>" . "\n";
            
            return $out;
	    }
	}
	
	public static function menuHistoria ()
	{
	    $items = (new Query())->select(['m.m_url', 'm.m_texto', 'mr.mr_rol_id'])
	        ->where(['mr.mr_rol_id' => Yii::$app->session['userRol'], 'm_ident' => 'menu_historia'])
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
        $out .= "<div class='card-header'> Historia del Paciente </div>" . "\n";
        $out .= Nav::widget([
            'items' => $item,
            'options' => ['class' => 'nav flex-column']
        ]);
        $out .= "</div><br>" . "\n";
        
        return $out;
	}
	
	public static function pacienteInforma()
	{
	    if (isset(Yii::$app->session['p']) && isset(Yii::$app->session['e']))
	    {
    	    $pac = PacienteModel::find()->with('antecedente', 'episodios')
    	                           ->where(['pac_id' => Yii::$app->session['p']])->one();
    	    $epis = EpisodioModel::find()->with('iq', 'episodiodx')
    	                            ->where(['epis_id' => Yii::$app->session['e']])->one();
    	    
            $ep = [];
            foreach ($pac->episodios as $e)
            {
                $ep[] = ['episodio' => $e['episLista']];
            }
            $out = Html::tag('b', Html::a('Episodios', '/clinica/historia/episodio')).
            Html::ul(ArrayHelper::getColumn($ep, function ($element) {
                return $element['episodio']; }));
    	                            
    	    $b = []; $out .= "<hr>";
            foreach ($pac->antecedente as $p)
            {
                $b[] = ['antec' => $p['antecLista']];
            }
            $out .= Html::tag('b', Html::a('Antecedentes', '/clinica/historia/antecedente')).
                    Html::ul(ArrayHelper::getColumn($b, function ($element) {
    	               return $element['antec']; }));
    	    
            $di = []; $out .= "<hr>";
    	    foreach ($epis->episodiodx as $d)
    	    {
    	        $di[] = ['dxLista' => $d['dxLista']];
    	    }
    	    $out .= Html::tag('b', Html::a('Diagnósticos', '/clinica/historia/diagnostico')).
    	            Html::ul(ArrayHelper::getColumn($di, function ($element) {
    	               return $element['dxLista']; }));
    	    
    	    $out .= "<hr>";
            $out .= Html::tag('b', Html::a('Intervenciones', '/clinica/historia/iq')).
                    Html::ul(ArrayHelper::getColumn($epis['iq'], function ($element) {
    	               return Yii::$app->formatter->asDate($element['iq_fecha']).': '.$element['iq_diagnostico']; }));
    	    
    	    return $out;
	    }
	}
	
	public static function logDatabase()
	{
		$data['la_pagina'] = Yii::$app->request->userIP . ' -- ' . Yii::$app->request->url;
		if (isset(Yii::$app->session['p'])) $data['la_pac_id'] = Yii::$app->session['p'];
		if (isset(Yii::$app->session['e'])) $data['la_epis_id'] = Yii::$app->session['e'];
		if (isset(Yii::$app->session['userLogin'])) $data['la_userlogin'] = Yii::$app->session['userLogin'];
		$model = new LogaccesoModel();
		$model->attributes = $data;
		$model->save();
	}
}