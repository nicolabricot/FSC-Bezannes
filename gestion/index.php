<?php

namespace lib;
use lib\content\Page;
use lib\content\Menu;

set_include_path('../');
spl_autoload_extensions('.php');
spl_autoload_register();

session_start();

require '../config/config.php';

if (!isset($_SESSION['authentificated']) || !$_SESSION['authentificated']) {
	header('Location: /login.php');
	exit();
}
else {
	
// Menu navigation
$mainMenu = new Menu();
	$mainMenu->addLink('Gestion', '/', 'wrench');
	$mainMenu->addLink('Activités', '/?page=activities', 'globe');
	$mainMenu->addLink('Membres', '/?page=members', 'user');
// Menu secondaire droite
$rightMenu = new Menu();
	$rightMenu->addLink('Feedback', 'mailto:nicolas+fsc@devenet.info', 'bullhorn');


// Contenu de la page
if (empty($_GET['page'])) $_GET['page'] = 'home';
str_replace("\0", '', $_GET['page']);
str_replace(DIRECTORY_SEPARATOR, '', $_GET['page']);

$controller = '../gestion/pages/'.$_GET['page'].'/controller.php';
$controller = file_exists($controller) ? $controller : '../pages/errors/404/controller.php';
require_once 'gestion/'.$controller;

$scripts = '';

?><!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title><?php echo ($page->url() != '/') ? $page->admin_title() .'&ndash; ' : null; ?>Gestion &ndash; FSC Bezannes</title>
		<meta name="author" content="FSC Bezannes" />
		<meta name="robots" content="NOINDEX, NOFOLLOW, NOARCHIVE" />
    <!--<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />-->
    <link rel="icon" type="image/png" href="/favicon.png" />
		<!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<link href="<?php echo _FSC_; ?>/css/bootstrap.min.css" rel="stylesheet" media="screen" />
		<link href="<?php echo _FSC_; ?>/css/bootstrap-fileupload.min.css" rel="stylesheet" media="screen" />
		<link href="<?php echo _FSC_; ?>/css/common.css" rel="stylesheet" media="screen" />
		<style>
			header {
				margin: 20px 0 0 0;
			}
			.page-header {
				margin-top: 0;
			}
			footer hr {
				margin-bottom: 10px;
			}
		</style>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="<?php echo _FSC_; ?>/css/bootstrap-responsive.min.css" rel="stylesheet" />
	</head>

	<body>
		
		<!-- menu -->
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container-fluid">
				<!--<a class="brand" href="/"><span class="fsc-blue">F</span><span class="fsc-green">S</span><span class="fsc-orange">C</span></a>-->
				<ul class="nav"><?php echo $mainMenu->display($page->url()); ?></ul>
				<!-- user -->
				<ul class="nav pull-right">
					<li class="divider-vertical"></li>
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $_SESSION['user_name']; ?>
						<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="<?php echo _FSC_; ?>" target="_blank">FSC <i class="icon-share-alt"></i></a></li>
						<li class="divider"></li>
						<li><a href="/login.php?logout"><i class="icon-off"></i> Déconnexion</a></li>
					</ul>
					</li>
				</ul>
				<ul class="nav pull-right"><?php echo $rightMenu->display($page->url()); ?></ul>
				</div>
			</div>
		</div>
		<!-- /menu -->
		
		<header class="container">
			<?php echo $page->breadcrumb(); ?>
		</header>
		
		<!-- container -->
		<div class="container">
			
			<!-- messages -->
			<?php
				if (isset($_SESSION['msg'])) {
					echo '<div class="row"><div class="span8 offset2">', $_SESSION['msg'], '</div></div>';
					unset($_SESSION['msg']);
				}
			?>
			<!-- /messages -->
			
			<?php
				include dirname($controller) . '/view.php';
			?>   
		</div>
		<!-- /container -->
		
		<!-- footer -->
		<footer>
			<hr />
			<div class="container">
				<p class="pull-left">&copy; 2012-<?php echo date('Y'); ?> &mdash; Foyer Social et Culturel de Bezannes</p>
			</div>
		</footer>
		<!-- /footer -->
		
		<script src="<?php echo _JQUERY_; ?>"></script>
		<script src="<?php echo _FSC_; ?>/js/bootstrap.min.js"></script>
		<?php
			echo (isset($scripts) ? $scripts : null);
			echo (_ANALYTICS_GESTION_ ? "
				<script type=\"text/javascript\">
					var _gaq = _gaq || [];
					_gaq.push(['_setAccount', 'UA-37435384-2']);
					_gaq.push(['_trackPageview']);
				
					(function() {
						var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
						ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
					})();
				</script>": null);
		?>
	</body>
</html>
<?php
	}
?>