<?php

/**
 * Esta es la clase madre que implementan todas las demás clases
 * del directorio historia\models.
 * 
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\historia\models;

use yii\db\ActiveRecord;
use Yii;

class HistoriaModel extends ActiveRecord
{    
    public function init()
    {
        parent::init();
        
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'defaultVals']);
    }
    
    public function defaultVals()
    {
        foreach ($this->attributes as $a => $i)
        {
            $varSesion = $this->sessionVals();
            foreach ($varSesion as $k => $v)
            {
                if (substr_count($a, $k) > 0) $this->$a = $v;
            }
        }
    }
    
    private function sessionVals ()
    {
        $out = array();
        $out['userlogin'] = Yii::$app->session['userLogin'] ?? '';
        $out['pac_id'] = Yii::$app->session['p'] ?? '';
        $out['epis_id'] = Yii::$app->session['e'] ?? '';
        return $out;
    }
}

