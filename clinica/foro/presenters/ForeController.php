<?php

namespace app\modules\foro\controllers;

use app\controllers\ParagalenoController;
use yii\grid\GridView;
use app\modules\foro\models\Articulo;
use yii\data\ActiveDataProvider;
use yii\bootstrap4\Html;
use app\modules\foro\models\Comentario;
use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use dslibs\helpers\Camp;
use dslibs\admin\models\UsuarioModel;

class ForeController extends ParagalenoController
{
	public $d;
	
	public function init()
	{
	    parent::init();

	    $lista = Articulo::find()->orderBy('fdc desc')->all();
	    $listaMap = ArrayHelper::map($lista, 'id', 'titulo');
	    
	    $this->d['c']['row']['c.3'][] = Html::tag('h2', 'Foro Enfermería<br><br>'.Camp::botonReturn('/foro/fore/articulo', 'Nuevo Tema'),
	            ['align' => 'center']) . Html::ul($listaMap, ['item' => function ($item, $index) {
	                return Html::tag('hr') . Html::tag('h5', Html::a($item, ['/foro/fore/tema', 'id' => $index])); }
	                ]);
	}
    
    public function actionIndex ()
    {
        $this->d['c']['row']['b.9'] = $this->gridComentarios();
        return $this->render('//template', $this->d);
    }
    
    public function actionTema ($id = false)
    {
        if ($id)
        {
            Yii::$app->session['e_a_id'] = $id;
            $titulo = (new Query())->select('titulo')->from('e_articulo')
                                   ->where(['id' => $id])->one();
            Yii::$app->session['e_a_titulo'] = $titulo['titulo'];
        }
        
        $this->d['c']['row']['b.9'][] = $this->gridTema(Yii::$app->session['e_a_id']);
        $this->d['c']['row']['b.9'][] = $this->gridComentario(Yii::$app->session['e_a_id']);
        $this->d['c']['row']['b.9'][] = $this->formComentario();
        
        return $this->render('//template', $this->d);
    }
    
    public function actionArticulo ($id = false)
    {
        if ($data = Yii::$app->request->post())
        {
            if (isset($data['id']) && $data['id'] !== '')
            {
                $model = Articulo::findOne($data['id']);
                $model->attributes = $data;
                if ($data['action'] == 'save') $model->save();
                if ($data['action'] == 'delete') $model->delete();
            }
            elseif ($data['id'] == '')
            {
                $model = new Articulo();
                $data['autor'] = Yii::$app->session['userId'];
                $model->attributes = $data;
                $model->save();
                
                $cont['autor'] = Yii::$app->session['usuario'];
                $cont['titulo'] = $model->titulo;
                $cont['texto'] = $model->texto;
                $this->correoNotifica($cont, 'Artículo');
            }
                Yii::$app->session['e_a_id'] = $model->id;
                return $this->redirect('/foro/fore/tema');
        }
        
        $this->d['c']['row']['a.9'][] = $this->formArticulo($id);
        return $this->render('//template', $this->d);
    }
    
    public function actionComentario ($id = FALSE)
    {
    if ($data = Yii::$app->request->post())
        {
            if (isset($data['id']) && $data['id'] != '')
            {
                $model = Comentario::findOne($data['id']);
                $model->attributes = $data;
                if ($data['action'] == 'save') $model->save();
                if ($data['action'] == 'delete') $model->delete();
            }
            else 
            {
                $model = new Comentario();
                $data['articulo_id'] = Yii::$app->session['e_a_id'];
                $data['autor'] = Yii::$app->session['userId'];
                $model->attributes = $data;
                $model->save();
                
                $cont['autor'] = Yii::$app->session['usuario'];
                $cont['titulo'] = Yii::$app->session['e_a_titulo'];
                $cont['texto'] = $model->texto;
                $this->correoNotifica($cont, 'Comentario');
            }
            return $this->redirect('/foro/fore/tema');
        }
        
        $this->d['c']['row']['a.9'][] = $this->formCambiaComentario($id);
        return $this->render('//template', $this->d);
    }
    
    private function gridTema ($id = false)
    {
        $model = Articulo::find()->where(['id' => $id])->with('autorNombre');
        $data = new ActiveDataProvider(['query' => $model]);
        $out = GridView::widget([
                'dataProvider' => $data,
                'summary' => '',
                'showHeader' => false,
                'tableOptions' => ['class' => 'table table-default'],
                'beforeRow' => function ($model) {
                    $out = html::tag('h1', $model->titulo) . Html::tag('h4', $model->autorNombre['Nombres']) .
                           Yii::$app->formatter->asDate($model->fdc) . ' -- ' .
                           Yii::$app->formatter->asTime($model->fdc) . '<br>';
                    if ($model->autor == Yii::$app->session['autorId'])
                    {
                        $out .= Html::tag('h5', Html::a('Editar', ['/foro/fore/articulo', 'id' => $model->id]));
                    }
                    return $out;
                },
                'columns' => [
                        ['attribute' => 'titulo', 'content' => function ($model) use ($id) {
                            return Html::tag('p', $model->texto);
                        }],
                ]
        ]);
        return $out;
    }
    
    private function gridComentario ($id = false)
    {
        $model = $id ? Comentario::find()->where(['articulo_id' => $id])->with('autorNombre') :
                Comentario::find()->with('autorNombre');
        $data = new ActiveDataProvider(['query' => $model]);
        $out = GridView::widget([
                'dataProvider' => $data,
                'summary' => '',
                'tableOptions' => ['class' => 'table table-striped'],
                'columns' => [
                        ['attribute' => 'autor', 'label' => 'Comentarios', 'content' => function ($model) {
                            $out = Html::tag('div', $model->autorNombre['Nombres'].'<br>'.
                                   Yii::$app->formatter->asDate($model->fdc).'<br>'.
                                   Yii::$app->formatter->asTime($model->fdc), ['align' => 'center']);
                            if ($model->autor == Yii::$app->session['autorId'])
                            {
                                $out .= Html::tag('h5', Html::a('Editar', ['/foro/fore/comentario', 'id' => $model->id]),
                                        ['align' => 'center']);
                            }
                            return $out;
                        }],
                        ['attribute' => 'c_texto', 'label' => '', 'content' => function ($model) {
                            return $model->texto;
                        }],
                ]
        ]);
        return $out;
    }
    
    private function gridComentarios ()
    {
        $model = Comentario::find()->with('autorNombre', 'articulo')->orderBy('fdc desc');
        $data = new ActiveDataProvider(['query' => $model]);
        $out = GridView::widget([
                'dataProvider' => $data,
                'caption' => Html::tag('h2', 'Comentarios Recientes'),
                'summary' => '',
                'tableOptions' => ['class' => 'table table-striped'],
                'columns' => [
                        ['attribute' => 'autor', 'label' => '', 'content' => function ($model) {
                            return  Html::tag('div', Html::a("<b>".$model->articulo['titulo']."</b>", ['/foro/fore/tema',
                                    'id' => $model->articulo['id']]).'<br><br>'.
                                    $model->autorNombre['Nombres'].'<br>'. Yii::$app->formatter->asDate($model->fdc).'<br>'.
                                    Yii::$app->formatter->asTime($model->fdc), ['align' => 'center']);
                        }],
                        ['attribute' => 'c_texto', 'label' => '', 'content' => function ($model) {
                            return $model->texto;
                        }],
                ]
        ]);
        return $out;
    }
    
    private function formComentario ()
    {
        $out = Html::tag('h4', 'Añade un comentario ...').
        Html::beginForm('/foro/fore/comentario', 'post').
        Camp::ckeditor('texto').
        Camp::botonSave().
        Html::endForm();
        return $out;
    }
    
    private function formArticulo ($id = false)
    {
        $model = $id ? Articulo::findOne($id) : new Articulo();
        $out = Html::tag('h3', 'Tema de Foro').
        Html::beginForm('/foro/fore/articulo', 'post').
        Html::hiddenInput('id', $model->id).
        Camp::textInput('titulo', $model->titulo, 'Título').
        Camp::ckeditor('texto', $model->texto, 'Texto').
        Camp::botonesNormal('/foro/fore', $id).
        Html::endForm();
        return $out;
    }
    
    private function formCambiaComentario ($id = false)
    {
        $model = Comentario::findOne($id);
        $out = Html::tag('h3', 'Modifica tu comentario').
        Html::beginForm('/foro/fore/comentario', 'post').
        Html::hiddenInput('id', $model->id).
        Camp::ckeditor('texto', $model->texto, 'Texto del comentario').
        Camp::botonesNormal('/foro/fore/tema', $id).
        Html::endForm();
        return $out;
    }
    private function correoNotifica ($cont, $tipo)
    {
        $usuario = UsuarioModel::findAll(['user_erecibe' => 1]);
        $nombre = Yii::$app->params['nombre'];
        foreach ($usuario as $u)
        {
            $objeto = "Nuevo $tipo en foro de Enfermería $nombre";
            $contenido = "Apreciado $u->user_nom $u->user_apell1:<br><br>
        	Se ha publicado un nuevo $tipo en el foro de Enfermería de $nombre<br><br>
        	Título: ".$cont['titulo']."<br><br>
        	Texto del artículo es:<br>".$cont['texto']."<br><br>
        	Autor: ".$cont['autor']."<br><br>
            Saludos cordiales<br>
            Enfermería de $nombre";
    
        	Yii::$app->mailer->compose()
        	->setFrom(Yii::$app->params['adminEmail'])
        	->setTo($u->user_email)
        	->setSubject($objeto)
        	->setTextBody('Plain text content')
        	->setHtmlBody($contenido)
        	->send();
        }
    }
}