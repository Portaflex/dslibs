<?php

namespace app\modules\foro\controllers;

use app\controllers\ParagalenoController;
use app\modules\foro\models\ForoArticulo;
use app\modules\foro\models\ForoComentario;
use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use dslibs\helpers\Camp;
use dslibs\admin\models\UsuarioModel;

class ForoController extends ParagalenoController
{
	public $d;
    
    public function init ()
    {
        parent::init();
        
        $lista = ForoArticulo::find()->orderBy('a_fdc desc')->all();
        $listaMap = ArrayHelper::map($lista, 'a_id', 'a_titulo');
        
        $this->d['c']['row']['b.3'][] = Html::tag('h3', 'Temas de Foro<br><br>'.Camp::botonReturn('/foro/foro/articulo', 'Nuevo Tema'),
                ['align' => 'center']) . Html::ul($listaMap, ['item' => function ($item, $index) {
                    return Html::tag('hr') . Html::tag('h5', Html::a($item, ['/foro/foro/tema', 'id' => $index])); }
                    ]);
        
    }
    
    public function actionIndex ()
    {
        $this->d['c']['row']['a.9'] = $this->gridComentarios();
        
        $lista = ForoArticulo::find()->orderBy('a_fdc desc')->all();
        $listaMap = ArrayHelper::map($lista, 'a_id', 'a_titulo');
        
        return $this->render('//template', $this->d);
    }
    
    public function actionTema ($id = false)
    {
        if ($id)
        {
            Yii::$app->session['a_id'] = $id;
            $titulo = (new Query())->select('a_titulo')->from('f_articulo')
                                   ->where(['a_id' => $id])->one();
            Yii::$app->session['a_titulo'] = $titulo['a_titulo'];
        }
        
        $this->d['c']['row']['a.9'][] = $this->gridTema(Yii::$app->session['a_id']);
        $this->d['c']['row']['a.9'][] = $this->gridComentario(Yii::$app->session['a_id']);
        $this->d['c']['row']['a.9'][] = $this->formComentario();
        
        return $this->render('//template', $this->d);
    }
    
    public function actionArticulo ($id = false)
    {
        if ($data = Yii::$app->request->post())
        {
            if (isset($data['a_id']) && $data['a_id'] !== '')
            {
                $model = ForoArticulo::findOne($data['a_id']);
                $model->attributes = $data;
                if ($data['action'] == 'save') $model->save();
                if ($data['action'] == 'delete')
                {
                    $model->delete();
                    return $this->redirect('/foro/foro');
                }
            }
            else
            {
                $model = new ForoArticulo();
                $data['a_autor_id'] = Yii::$app->session['userId'];
                $model->attributes = $data;
                $model->save();
                
                $cont['autor'] = Yii::$app->session['usuario'];
                $cont['titulo'] = $model->a_titulo;
                $cont['texto'] = $model->a_texto;
                $this->correoNotifica($cont, 'Artículo');
            }
            Yii::$app->session['a_id'] = $model->a_id;
            return $this->redirect('/foro/foro/tema');
        }
        
        $this->d['c']['row']['a.9'][] = $this->formArticulo($id);
        return $this->render('//template', $this->d);
    }
    
    public function actionComentario ($id = FALSE)
    {
        if ($data = Yii::$app->request->post())
        {
            if (isset($data['c_id']) && $data['c_id'] != '')
            {
                $model = ForoComentario::findOne($data['c_id']);
                $model->attributes = $data;
                if ($data['action'] == 'save') $model->save();
                if ($data['action'] == 'delete') $model->delete();
            }
            else 
            {
                $model = new ForoComentario();
                $data['c_articulo_id'] = Yii::$app->session['a_id'];
                $data['c_autor_id'] = Yii::$app->session['userId'];
                $model->attributes = $data;
                $model->save();
                
                $cont['autor'] = Yii::$app->session['usuario'];
                $cont['titulo'] = Yii::$app->session['a_titulo'];
                $cont['texto'] = $model->c_texto;
                $this->correoNotifica($cont, 'Comentario');
            }
            return $this->redirect('/foro/foro/tema');
        }
        
        $this->d['c']['row']['a.9'][] = $this->formCambiaComentario($id);
        return $this->render('//template', $this->d);
    }
    
    private function gridTema ($id = false)
    {
        $model = ForoArticulo::find()->where(['a_id' => $id])->with('autor', 'tema');
        $data = new ActiveDataProvider(['query' => $model]);
        $out = GridView::widget([
                'dataProvider' => $data,
                'summary' => '',
                'showHeader' => false,
                'tableOptions' => ['class' => 'table table-default'],
                'beforeRow' => function ($model) {
                    $out = html::tag('h1', $model->a_titulo) . Html::tag('h4', $model->autor['Nombres']) .
                           Yii::$app->formatter->asDate($model->a_fdc) . ' -- ' .
                           Yii::$app->formatter->asTime($model->a_fdc) . '<br>' ;
                    if ($model->a_autor_id == Yii::$app->session['autorId'])
                    {
                        $out .= Html::tag('h5', Html::a('Editar', ['/foro/foro/articulo', 'id' => $model->a_id]));
                    }
                    return $out;
                },
                'columns' => [
                        ['attribute' => 'a_titulo', 'content' => function ($model) use ($id) {
                            return Html::tag('p', $model->a_texto);
                        }],
                ]
        ]);
        return $out;
    }
    
    private function gridComentario ($id = false)
    {
        $model = $id ? ForoComentario::find()->where(['c_articulo_id' => $id])->with('autor') :
                ForoComentario::find()->with('autor');
        $data = new ActiveDataProvider(['query' => $model]);
        $out = GridView::widget([
                'dataProvider' => $data,
                'summary' => '',
                'tableOptions' => ['class' => 'table table-striped'],
                'columns' => [
                        ['attribute' => 'c_autor_id', 'label' => 'Comentarios', 'content' => function ($model) {
                            $out = Html::tag('div', $model->autor['Nombres'].'<br>'.
                                   Yii::$app->formatter->asDate($model->c_fdc).'<br>'.
                                   Yii::$app->formatter->asTime($model->c_fdc), ['align' => 'center', 'style']);
                            if ($model->c_autor_id == Yii::$app->session['autorId'])
                            {
                                $out .= Html::tag('h5', Html::a('Editar', ['/foro/foro/comentario', 'id' => $model->c_id]),
                                        ['align' => 'center']);
                            }
                            return $out;
                        }],
                        ['attribute' => 'c_texto', 'label' => '', 'content' => function ($model) {
                            return $model->c_texto;
                        }],
                ]
        ]);
        return $out;
    }
    
    private function gridComentarios ()
    {
        $model = ForoComentario::find()->with('autor', 'articulo', 'tema')->orderBy('c_fdc desc');
        $data = new ActiveDataProvider(['query' => $model]);
        $out = GridView::widget([
                'dataProvider' => $data,
                'caption' => Html::tag('h2', 'Comentarios Recientes'),
                'summary' => '',
                'tableOptions' => ['class' => 'table table-striped'],
                'columns' => [
                        ['attribute' => 'c_autor_id', 'label' => '', 'content' => function ($model) {
                            return  Html::tag('div', Html::a("<b>".$model->articulo['a_titulo']."</b>", ['/foro/foro/tema',
                                    'id' => $model->articulo['a_id']]).'<br><br>'.
                                    $model->autor['Nombres'].'<br>'. Yii::$app->formatter->asDate($model->c_fdc).'<br>'.
                                    Yii::$app->formatter->asTime($model->c_fdc), ['align' => 'center']);
                        }],
                        ['attribute' => 'c_texto', 'label' => '', 'content' => function ($model) {
                            return $model->c_texto;
                        }],
                ]
        ]);
        return $out;
    }
    
    private function formComentario ()
    {
        $out = Html::tag('h4', 'Añade un comentario ...').
        Html::beginForm('/foro/foro/comentario', 'post').
        Camp::ckeditor('c_texto').
        Camp::botonSave().
        Html::endForm();
        return $out;
    }
    
    private function formArticulo ($id = false)
    {
        $model = $id ? ForoArticulo::findOne($id) : new ForoArticulo();
        $out = Html::tag('h3', 'Tema de Foro').
        Html::beginForm('/foro/foro/articulo', 'post').
        Camp::textInput('a_titulo', $model->a_titulo, 'Título').
        Camp::ckeditor('a_texto', $model->a_texto, 'Texto').
        Html::hiddenInput('a_id', $model->a_id).
        Camp::botonesNormal('/foro/foro/tema', $id).
        Html::endForm();
        return $out;
    }
    
    private function formCambiaComentario ($id = false)
    {
        $model = ForoComentario::findOne($id);
        $out = Html::tag('h3', 'Modifica tu comentario').
        Html::beginForm('/foro/foro/comentario', 'post').
        Html::hiddenInput('c_id', $model->c_id).
        Camp::ckeditor('c_texto', $model->c_texto, 'Texto del comentario').
        Camp::botonesNormal('/foro/foro/tema', $id).
        Html::endForm();
        return $out;
    }

    private function correoNotifica ($cont, $tipo)
    {
        $usuario = UsuarioModel::findAll(['user_recibe' => 1]);
        $nombre = Yii::$app->params['nombre'];
        foreach ($usuario as $u)
        {
            $objeto = "Nuevo $tipo en foro $nombre";
            $contenido = "Apreciado $u->user_nom $u->user_apell1:<br><br>
        	Se ha publicado un nuevo $tipo en el foro $nombre<br><br>
        	Título: ".$cont['titulo']."<br>
        	Texto del artículo es: ".$cont['texto']."<br>
        	Autor: ".$cont['autor']."<br><br>
            Saludos cordiales<br>
            $nombre";
    
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