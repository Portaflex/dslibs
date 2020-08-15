<?php

namespace dslibs\cms\helpers;

use dslibs\cms\admin\models\PublicacionModel;
use dslibs\helpers\Camp;
use dslibs\helpers\Lista;
use Yii;
use yii\base\Behavior;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\db\Query;
use dslibs\cms\admin\models\ContactForm;

class InformaCms extends Behavior
{	
    public function lista ($tabla, $id, $nombre, $cond = false)
    {
        return Lista::lista($tabla, $id, $nombre, $cond);
    }
    
    public static function menuContenido ($ident = 'menu_contenido')
    {
        $items = (new Query())->select(['m_url', 'm_texto'])
        ->where(['m_ident' => $ident])->from('menu')->orderBy('m_orden')->all();
        
        $item = array();
        
        foreach ($items as $i)
        {
            $item[] = [
                'label' => $i['m_texto'],
                'url' => $i['m_url'],
            ];
        }
        
        if (Yii::$app->session['userRol'] == 1)
        {
            $item[] = [
                'label' => 'AdminForo',
                'url' => '/foro/admin'
            ];
        }
        
        if (Yii::$app->session['userRol'] == 1)
        {
            $item[] = [
                'label' => 'Gestion',
                'url' => '/admin/su-usuario'
            ];
        }
        
        $out = Nav::widget([
            'items' => $item,
            'options' => ['class' => 'navbar-nav navbar-left', 'style' => 'font-weight:bold; font-size:18px;']
        ]);
        
        return $out;
    }
    
    public static function menuTema ($tema_id)
    {
        $items = PublicacionModel::findAll(['p_categoria_url' => $tema_id, 'p_publicado' => 1]);
        
        $item = array();
        
        foreach ($items as $i)
        {
            $item[] = [
                'label' => $i['p_titulo'],
                'url' => '/pagina/'.$i['p_titulo_url'],
            ];
        }
        $out = '<div class="card-body">';
        $out .= Nav::widget([
            'items' => $item,
            'options' => ['class' => 'nav nav-pills nav-stacked']
        ]);
        $out .= '</div>';
        return $out;
        
    }
    
    public static function menuGestion ()
    {
        if (isset(Yii::$app->session['userRol']))
        {
            $items = (new Query())->select(['m_curl', 'm_texto'])
            ->where(['m_parent' => 3])->from('menu')->orderBy('m_orden')->all();
            
            $item = array();
            
            foreach ($items as $i)
            {
                $item[] = [
                    'label' => $i['m_texto'],
                    'url' => $i['m_curl'],
                ];
            }
            
            if (Yii::$app->session['userRol'] == 1)
            {
                $item[] = [
                    'label' => 'Gestion',
                    'url' => '/admin/su-usuario'
                ];
            }
            
            $out = Nav::widget([
                'items' => $item,
                'options' => ['class' => 'navbar-nav navbar-left']
            ]);
            return $out;
        }
    }
    
    public static function menuAdmin ()
    {
        if (isset(Yii::$app->session['userRol']))
        {
            $items = (new Query())->select(['m.m_url', 'm.m_texto', 'mr.mr_rol_id'])
            ->where(['mr.mr_rol_id' => Yii::$app->session['userRol'], 'm_parent' => 2])
            ->from('menu_rol mr')->orderBy('m_texto')
            ->leftJoin('menu m', 'mr.mr_menu_id = m.m_id')->all();
            
            $item = array();
            
            foreach ($items as $i)
            {
                $item[] = [
                    'label' => $i['m_texto'],
                    'url' => $i['m_url'],
                ];
            }
            $out = Nav::widget([
                'items' => $item,
                'options' => ['class' => 'card']
            ]);
            return $out;
        }
    }
    
    public static function contactForm()
    {
        $model = new ContactForm();
        
        if ($data = Yii::$app->request->post())
        {
            $data['reCaptcha'] = $data['g-recaptcha-response'];
            $data['body'] = Yii::$app->request->userIP.' - '.$data['body'];
            $model->attributes = $data;
            
            if ($model->contact('admin@clinicadecot.es'))
            {
                Yii::$app->session->setFlash('contacto');
            }
        }
        
        $out = Html::tag('h3', 'Contacto').
        Html::beginForm('', 'post'). "\n".
        Html::errorSummary($model).
        Html::label(Yii::$app->session->hasFlash('contacto') ? 'Formulario enviado correctamente <br>' : '') . "\n".
        Camp::textInput('name', $model->name, 'Nombre'). "\n".
        Camp::textInput('email', $model->email, 'E-mail'). "\n".
        Camp::textArea('body', $model->body, 'Texto')."<br>". "\n".
        "<div class='g-recaptcha' data-sitekey='6LdhZykUAAAAAOUZyJ-NcDzuKReXH3qDMt612AZ0'
            data-size='compact'></div>".
        Camp::botonSend(). "\n".
        Html::endForm();
            
            return $out;
    }
}