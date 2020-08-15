<?php

namespace dslibs\cms\admin\models;

use yii\data\ActiveDataProvider;
use dslibs\admin\models\MenuModel;

class PublicacionModel extends AdminModel
{
    public static function tableName()
    {
        return 'publicacion';
    }
    
    
    public function rules()
    {
        return [
            [['p_tipo', 'p_categoria_url', 'p_titulo_url'], 'required'],
            [['p_coloca', 'p_tipo', 'p_categoria'], 'default', 'value' => null],
            [['p_id', 'p_coloca', 'p_tipo', 'p_categoria', 'p_publicado', 'p_orden'], 'integer'],
            [['p_texto', 'p_texto_plano', 'p_texto_resumen'], 'string'],
            [['p_fecha'], 'safe'],
            [['p_titulo', 'p_titulo_url', 'p_autor', 'p_taxonomia',
                'p_categoria_url'], 'string', 'max' => 255],
            [['p_id'], 'unique'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'p_id' => 'ID',
            'p_coloca' => 'Colocación',
            'p_tipo' => 'Tipo',
            'p_categoria' => 'Categoría',
            'p_titulo' => 'Título',
            'p_titulo_url' => 'Título URL',
            'p_texto' => 'Texto',
            'p_fecha' => 'Fecha',
            'p_autor' => 'Autor',
            'p_taxonomia' => 'Taxonomia',
            'p_publicado' => 'Publicado',
            'p_orden' => 'Orden',
            'p_categoria_url' => 'Categoría URL',
            'p_texto_plano' => 'Texto Plano',
            'p_texto_resumen' => 'Resumen'
        ];
    }
    
    public function getColoca()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'p_coloca'])
        ->where(['c.m_parent' => 35]);
    }
    
    public function getTipo()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'p_tipo'])
        ->where(['t.m_parent' => 32]);
    }
    
    public function getCategoria()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'p_categoria'])
        ->where(['ca.m_parent' => 5]);
    }
    
    public function getPublicado()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'p_publicado'])
        ->where(['p.m_parent' => 1]);
    }
    
    public function search($params)
    {
        $query = self::find()->joinWith(['tipo t', 'coloca c', 'publicado p']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 40
            ],
            'sort' => [
                'attributes' => [
                    'tipo.m_texto' => [
                        'asc' => ['t.m_texto' => SORT_ASC],
                        'desc' => ['t.m_texto' => SORT_DESC]
                    ],
                    'coloca.m_texto' => [
                        'asc' => ['c.m_texto' => SORT_ASC],
                        'desc' => ['c.m_texto' => SORT_DESC]
                    ],
                    'p_titulo_url' => [
                        'asc' => ['p_titulo_url' => SORT_ASC],
                        'desc' => ['p_titulo_url' => SORT_DESC]
                    ],
                    'p_categoria_url' => [
                        'asc' => ['p_categoria_url' => SORT_ASC],
                        'desc' => ['p_categoria_url' => SORT_DESC]
                    ],
                    'publicado.m_texto' => [
                        'asc' => ['p.m_texto' => SORT_ASC],
                        'desc' => ['p.m_texto' => SORT_DESC]
                    ], 'p_titulo', 'p_id', 'p_orden'
                ]
            ]
        ]);
        
        $this->attributes = $params;
        
        //if (!$this->validate()) return $dataProvider;
        
        $query->andFilterWhere([
            'p_tipo' => $this->p_tipo,
            'p_publicado' => $this->p_publicado,
            'p_coloca' => $this->p_coloca,
            'p_categoria' => $this->p_categoria,
            'p_categoria_url' => $this->p_categoria_url,
            'p_titulo_url' => $this->p_titulo_url
        ]);
        
        $query->andFilterWhere(['ilike', 'p_titulo', $this->p_titulo]);
        
        return $dataProvider;
    }
}
