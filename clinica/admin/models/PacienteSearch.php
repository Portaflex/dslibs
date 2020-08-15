<?php

/**
 * Esta es la clase implementa el modelo de búsqueda de pacientes.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use yii\data\ActiveDataProvider;

class PacienteSearch extends PacienteModel
{

    public function rules ()
    {
        return [
           [['pac_id','pac_activo','pac_recibe'],'integer'],
           [['pac_nom','pac_apell1','pac_apell2','pac_fnac', 'pac_nif','pac_numss',
             'pac_direcc','pac_poblac','pac_provincia','pac_cpostal','pac_telefo',
             'pac_telefo2','pac_email','pac_observac','pac_fechacreac','pac_fdc',
             'pac_fdu','pac_userlogin','pac_login','pac_pw','pac_grupo','pac_antec'],'safe']];
    }

    public function search ($params)
    {
        $query = PacienteModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        $this->load($params);

        if (! $this->validate())
        { // uncomment the following line if you do not
          // want to return any
          // records when validation fails
          // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pac_fnac' => $this->pac_fnac,
            'pac_id' => $this->pac_id
        ]);

        // Usamos la función unaccent con los nombre y apellidos. Hay que instalarla para poder usarla:
        // http://www.yoymiyo.es/instalar-unaccent-plugin-en-postgresql-9-1/
        
        $query->andFilterWhere(['ilike','pac_nom', $this->pac_nom])
            ->andFilterWhere(['ilike','pac_apell1',$this->pac_apell1])
            ->andFilterWhere(['ilike','pac_apell2',$this->pac_apell2])
            ->andFilterWhere(['ilike','pac_telefo', $this->pac_telefo])
            ->andFilterWhere(['ilike','pac_telefo2',$this->pac_telefo2])
            ->andFilterWhere(['ilike','pac_email',$this->pac_email]);

        return $dataProvider;
    }
}
