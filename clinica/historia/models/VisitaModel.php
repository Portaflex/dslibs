<?php

namespace dslibs\clinica\historia\models;

use Yii;
use yii\db\Query;
use yii\bootstrap4\Html;

class VisitaModel extends HistoriaModel
{
    public static function tableName()
    {
        return 'consulta';
    }

    public function rules()
    {
        return [
            //[['consulta_epis_id', 'consulta_pac_id'], 'required'],
            [['consulta_id', 'consulta_epis_id', 'consulta_pac_id', 'consulta_tipo'], 'integer'],
            [['consulta_notas', 'consulta_fotos'], 'string'],
        	[['consulta_fecha', 'consulta_fdc', 'consulta_fdu', 'consulta_epis_id', 'consulta_pac_id',
        		  'consulta_userlogin'], 'safe'],
            [['consulta_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'consulta_id' => 'Consulta ID',
            'consulta_epis_id' => 'Consulta Epis ID',
            'consulta_pac_id' => 'Consulta Pac ID',
            'consulta_tipo' => 'Consulta Tipo',
            'consulta_notas' => 'Consulta Notas',
            'consulta_fotos' => 'Consulta Fotos',
            'consulta_fecha' => 'Consulta Fecha',
            'consulta_fdc' => 'Consulta Fdc',
            'consulta_fdu' => 'Consulta Fdu',
            'consulta_userlogin' => 'Consulta Userlogin',
        ];
    }

    public static function v_visita($pac_id, $epis_id)
    {
    	return (new Query())->select([
    		'c.consulta_id', 'c.consulta_notas', 'c.consulta_id', 'c.consulta_userlogin',
    		'c.consulta_tipo', 'c.consulta_fecha', 'consulta_fdc',
    		'e.epis_id', 'p.pac_id', 'm.m_texto as tipo_consulta',
    		"((now()::text)::date - c.consulta_fecha) as dias", 'f.finan_empresa',
    		"concat_ws(' ', u.user_nom, u.user_apell1) as sanitario"
    	])->from('consulta c')
    	->leftJoin('usuario u', 'c.consulta_userlogin::text = u.user_login::text')
    	->leftJoin('menu m', "c.consulta_tipo = m.m_valor and m.m_ident = 'tipo_cita'")
    	->leftJoin('episodio e', 'c.consulta_epis_id = e.epis_id')
    	->leftJoin('financiador f', 'e.epis_finan_id = f.finan_id')
    	->leftJoin('paciente p', 'c.consulta_pac_id = p.pac_id')
    	->where(['consulta_pac_id' => $pac_id, 'consulta_epis_id' => $epis_id])
    	->orderBy('consulta_fecha DESC');
    }

    public static function notas ()
    {
    	$out = '';
    	$query = self::find()->where(['consulta_epis_id' => Yii::$app->session['e']])->orderBy('consulta_fdc')->all();
    	foreach ($query as $q)
    	{
    		$out .= Html::tag('p', Yii::$app->formatter->asDate($q->consulta_fdc).': '.$q->consulta_notas);
    	}
    	return $out;
    }
}
