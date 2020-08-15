<?php

/**
 * Esta es la clase implementa el modelo de citas.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use dslibs\admin\models\MenuModel;
use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class CitaModel extends AdminModel
{
    public static function tableName ()
    {
        return 'cita';
    }

    public function rules ()
    {
        return [
                [['cita_fecha','cita_hora'],'required'],
                [['cita_id','cita_ag_id','cita_epis_id','cita_pac_id','cita_sani_id',
                  'cita_cen_id','cita_estado','cita_tipo','cita_facturable','cita_aviso','cita_dep'],'integer'],
        		[['cita_fecha','cita_hora','cita_hllega','cita_hentra','cita_hsale',
        		  'cita_epis_id','cita_pac_id', 'cita_userlogin'],'safe'],
                [['cita_observaciones'],'string'],
                [['cita_userlogin'],'string','max' => 45],
                [['cita_link'],'string','max' => 255],
            ];
    }

    public function attributeLabels ()
    {
        return [
                'cita_id' => 'ID',
                'cita_fecha' => 'Fecha',
                'cita_hora' => 'Hora',
                'cita_ag_id' => 'CitaModel Ag ID',
                'cita_epis_id' => 'CitaModel Epis ID',
                'cita_pac_id' => 'Paciente',
                'cita_sani_id' => 'Sanitario',
                'cita_userlogin' => 'Userlogin',
                'cita_cen_id' => 'CitaModel Cen ID',
                'cita_estado' => 'Estado',
                'cita_tipo' => 'Tipo de cita',
                'cita_facturable' => 'Indica si visita es facturable: 1-si, 0-no.',
                'cita_hllega' => 'Hora de entrada',
                'cita_hentra' => 'CitaModel Hentra',
                'cita_hsale' => 'Hora de salida',
                'cita_observaciones' => 'Observaciones',
                'cita_link' => 'CitaModel Link',
                'cita_aviso' => 'CitaModel Aviso',
                'cita_dep' => 'CitaModel Dep',
        ];
    }
    
    public static function v_cita()
    {
        $q = (new Query())->select([
            'c.cita_fecha', 'c.cita_hora', 'c.cita_estado', 'c.cita_id',
            'c.cita_tipo', 'c.cita_sani_id', 'cita_observaciones', 'c.cita_pac_id', 'c.cita_link',
            'c.cita_dep', 'f.finan_empresa', "f.finan_id",
            "concat_ws(' ', p.pac_nom, p.pac_apell1, p.pac_apell2) as paciente", 'p.pac_telefo as telefono',
            'p.pac_id', 'e.epis_id', 'e.epis_estado', 'm.m_texto as estado', 'm1.m_texto as tipo',
            'm3.m_texto as departamento', 's.sani_apellido1 as sanitario', 's.sani_color',
            "CASE WHEN (c.cita_hentra IS NULL) THEN
	        ((('now'::text)::time(0) without time zone)::interval - (c.cita_hllega)::interval)
	        ELSE (c.cita_hentra - c.cita_hllega) END AS espera",
                "date_part('year'::text, age(now(), (p.pac_fnac)::timestamp with time zone)) AS edad"
        ])
        ->from('cita c')
        ->leftJoin('episodio e', 'c.cita_epis_id = e.epis_id')
        ->leftJoin('menu m', "c.cita_estado = m.m_valor and m.m_ident = 'estado_cita'")
        ->leftJoin('menu m1', "c.cita_tipo = m1.m_valor and m1.m_ident = 'tipo_cita'")
        ->leftJoin('sanitario s', 'c.cita_sani_id = s.sani_id')
        ->leftJoin('paciente p', 'c.cita_pac_id = p.pac_id')
        ->leftJoin('financiador f', 'e.epis_finan_id = f.finan_id')
        ->leftJoin('menu m3', "c.cita_dep = m3.m_valor and m3.m_ident = 'departamento'");
        
        return $q;
    }
    
    public function getEpisodio ()
    {
        return $this->hasOne(EpisodioModel::className(), ['epis_id' => 'cita_epis_id']);
    }
    
    public function getEstado ()
    {
        return $this->hasOne(MenuModel::className(),  ['m_valor' => 'cita_estado'])->where(['m_ident' => 'estado_cita']);
    }
    
    public function getTipo ()
    {
        return $this->hasOne(MenuModel::className(),  ['m_valor' => 'cita_tipo'])->where(['m_ident' => 'tipo_cita']);
    }
    
    public function getPaciente ()
    {
        return $this->hasOne(PacienteModel::className(), ['pac_id' => 'cita_pac_id']);
    }
    
    public function getSanitario ()
    {
        return $this->hasOne(SanitarioModel::className(), ['sani_id' => 'cita_sani_id']);
    }
    
    public function getFinanciador ()
    {
        return $this->hasOne(FinanciadorModel::className(),  ['finan_id' => 'epis_finan_id'])
        ->via('episodio');
    }
    
    public static function Form($id)
    {
        return self::v_cita()->where(['cita_id' => $id])->one();
    }
    
    public static function v_cita_calendar($fecha, $sani = false)
    {
        return (new Query())->distinct('cita_fecha')->from('cita')
        ->where(["cita_fecha" => $fecha, 'cita_sani_id' => $sani]);
    }
    
    public function recuento ()
    {
        return $this->v_cita()
        ->andWhere(['cita_fecha' => Yii::$app->session['cfecha']])
        ->andWhere(['cita_dep' => Yii::$app->session['dep']])
        ->andFilterWhere(['cita_sani_id' => Yii::$app->session['sani']])
        ->all();
    }
    
    public function searchAgenda ()
    {
        if ($data = Yii::$app->request->get()) Yii::$app->session['cita_params'] = $data;
        
        $params = Yii::$app->session['cita_params'];
        $page = isset($params['page']) ? $params['page'] - 1 : '';
        
        $query = self::v_cita()->andWhere([
            'c.cita_fecha' => Yii::$app->session['cfecha'],
            'c.cita_dep' => Yii::$app->session['dep']])->andFilterWhere([
            'c.cita_sani_id' => Yii::$app->session['sani']]);;
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
                'page' => $page
            ],
            'sort' => [
                'attributes' => [
                    'cita_hora', 'espera', 'cita_estado', 'tipo',
                    'finan_empresa', 'sanitario', 'paciente', 'edad', 'telefono'
                ],
                'defaultOrder' => ['cita_hora' => SORT_ASC],
                'enableMultiSort' => true
            ]
        ]);
        
        $this->load($params);
        
        if (! $this->validate()) return $dataProvider;
        
        return $dataProvider;
    }
    
    public function searchHistoria ()
    {
        $query = self::v_cita()->andWhere([
            'c.cita_pac_id' => Yii::$app->session['p'],
            'c.cita_epis_id' => Yii::$app->session['e'],
            'c.cita_dep' => Yii::$app->session['d']
        ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => [
                    'cita_fecha', 'cita_hora', 'cita_estado',
                    'cita_tipo', 'cita_sani_id'
                ],
                'defaultOrder' => ['cita_fecha' => SORT_ASC],
                'enableMultiSort' => true
            ],
        ]);
        
        $this->load(Yii::$app->request->queryParams);
        
        if (! $this->validate()) return $dataProvider;
        
        return $dataProvider;
    }
}
