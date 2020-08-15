<?php

/**
 * Esta es la clase madre que implementan todas las demás clases
 * del directorio admin.
 * 
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

use yii\db\ActiveRecord;
use Yii;

class AdminModel extends ActiveRecord
{    
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'defaultVals']);
    }
    
    public function defaultVals()
    {
        foreach ($this->attributes as $a)
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
        $out = [];
        $out['userlogin'] = Yii::$app->session['userLogin'] ?? '';
        return $out;
    }
}

