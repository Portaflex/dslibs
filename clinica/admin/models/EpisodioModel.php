<?php

/**
 * Esta es la clase implementa el modelo de episodios.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use yii\data\ActiveDataProvider;

class EpisodioModel extends AdminModel
{
	public $pac_nom;
	public $pac_apell1;
	public $pac_apell2;
	public $pac;
    
    public static function tableName ()
    {
        return 'episodio';
    }

    public function rules ()
    {
        return [
           //[['epis_pac_id'],'required'],
           [['epis_id','epis_pac_id','epis_finan_id','epis_estado','epis_dep'],'integer'],
           [['epis_fechaacc','epis_fechaabre','epis_fechacierra','epis_fdc','epis_fdu', 'epis_userlogin',
             'epis_pac_id', 'epis_expediente', 'pac', 'financiador', 'pac_nom', 'pac_apell1',
             'pac_apell2'],'safe'],
           [['epis_observ'],'string','max' => 250],
           [['epis_userlogin', 'epis_expediente'],'string','max' => 45],
           [['epis_id'],'unique'],
           ['epis_estado', 'default', 'value' => '1'],
           ['epis_fechaabre', 'default', 'value' => date('Y-m-d')],
       ];
    }

    public function attributeLabels ()
    {
        return [
                'epis_id' => 'ID',
                'epis_pac_id' => 'Paciente N.H.',
                'epis_finan_id' => 'Financiador',
                'epis_estado' => 'Estado del episodio',
                'epis_fechaacc' => 'Fecha del accidente',
                'epis_fechaabre' => 'Fecha de apertura',
                'epis_fechacierra' => 'Fecha de cierre',
                'epis_observ' => 'Observaciones',
                'epis_fdc' => 'Fecha',
                'epis_fdu' => 'Fdu',
                'epis_userlogin' => 'userlogin',
                'epis_dep' => 'Departamento',
                'epis_expediente' => 'Expediente',
        ];
    }

    public function getCitas ()
    {
        return $this->hasMany(CitaModel::className(),['cita_epis_id' => 'epis_id']);
    }

    public function getFinanciador ()
    {
        return $this->hasOne(FinanciadorModel::className(),['finan_id' => 'epis_finan_id']);
    }
    
    public function setFinanciador ($f)
    {
        $this->financiador = $f;
    }

    public function getPaciente ()
    {
    	return $this->hasOne(PacienteModel::className(),['pac_id' => 'epis_pac_id']);
    }
    
    public function search ($params)
    {
        $query = self::find()->joinWith(['paciente p', 'financiador f']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['epis_id', 'epis_pac_id', 'epis_fdc', 'epis_expediente',
                    'pac_nom' => [
                        'asc' => ['p.pac_nom' => SORT_ASC],
                        'desc' => ['p.pac_nom' => SORT_DESC]
                    ],
                    'pac_apell1' => [
                        'asc' => ['p.pac_apell1' => SORT_ASC],
                        'desc' => ['p.pac_apell1' => SORT_DESC]
                    ],
                    'pac_apell2' => [
                        'asc' => ['p.pac_apell2' => SORT_ASC],
                        'desc' => ['p.pac_apell2' => SORT_DESC]
                    ],
                    'financiador' => [
                        'asc' => ['f.finan_empresa' => SORT_ASC],
                        'desc' => ['f.finan_empresa' => SORT_DESC]
                    ],
                ]
            ]
        ]);
        
        $this->load($params);
        
        if (! $this->validate())
        { // uncomment the following line if you do not
            // want to return any
            // records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'epis_id' => $this->epis_id,
            'epis_pac_id' => $this->epis_pac_id,
            'epis_fdc' => $this->epis_fdc,
        ]);
        
        // Usamos la función unaccent con los nombre y apellidos. Hay que instalarla para poder usarla:
        // http://www.yoymiyo.es/instalar-unaccent-plugin-en-postgresql-9-1/
        
        $query->andFilterWhere(['ilike','p.pac_nom', $this->pac_nom]);
        $query->andFilterWhere(['ilike','p.pac_apell1', $this->pac_apell1]);
        $query->andFilterWhere(['ilike','p.pac_apell2', $this->pac_apell2]);
        $query->andFilterWhere(['ilike','f.finan_empresa', $this->financiador]);
        $query->andFilterWhere(['ilike','epis_expediente', $this->epis_expediente]);
        $query->andFilterWhere(['ilike', 'concat_ws (p.pac_nom, p.pac_apell1, p.pac_apell2)', $this->pac]);
        
        return $dataProvider;
    }
}
