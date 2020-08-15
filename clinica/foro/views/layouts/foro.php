<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap4\NavBar;
use app\assets\AppAsset;
use dslibs\clinica\helpers\InformaClinica;

AppAsset::register($this);
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags()?>
    <title><?php if (isset($this->context->titulo)) echo Html::encode($this->context->titulo); else echo 'Clínica de C.O.T.'; ?></title>
    <?php $this->head()?>
</head>
<body>
<?php $this->beginBody()?>

<div class="wrap">
    <?php
    NavBar::begin(
            [
                    'brandLabel' => "<img src='/images/logo_clinica.png' alt='Clínica de C.O.T.'></a>",
                    'brandUrl' => Yii::$app->homeUrl,
                    'options' => [
                            'class' => 'navbar-inverse navbar-fixed-top']]);
            echo InformaClinica::menuPrincipal();
    NavBar::end();
    ?>

    <!-- Campo modal -->
	<div class="modal fade" id="ds_modal">
  		<div class="modal-dialog">
    		<div class="modal-content">
      			<div class="modal-body" id="ds_mensaje">
      			</div>
    		</div>
  		</div>
	</div>
	<!-- fin campo modal -->
	<!-- Campo alert -->
	<div class="modal fade" id="ds_alert">
		<div class="modal-dialog">
			<div class="alert alert-success" id="ds_alerta"></div>
		</div>
	</div>
	<!-- fin campo alert -->

    <div class="container">
        <?= $content ?>
    </div>
	</div>

	<footer class="footer">
		<div class="container">
			<p class="pull-left">&copy; Paragaleno <?= date('Y') ?><br>
			</p>
			<p class="pull-right"><?php if (isset($_SESSION['usuario']))
			{
				echo $_SESSION['usuario'].'<br>';
				echo Html::a('Cerrar Sesion', '/login/logout');
			}?></p>
		</div>
	</footer>

<?php $this->endBody()?>
</body>
</html>
<?php $this->endPage()?>
