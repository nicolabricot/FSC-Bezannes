<?php

namespace lib;
use lib\preinscriptions\Preinscription;
use lib\users\UserInscription;

set_include_path('../../');
spl_autoload_extensions('.php');
spl_autoload_register();
error_reporting (0);

session_name('fsc_gestion');
session_start();

require '../../config/config.php';

if (!isset($_SESSION['authentificated']) || !$_SESSION['authentificated']) {
  header('Location: '. _GESTION_ .'/login.php');
  exit();
}
else {

  header('Content-type: application/json');

  $data = array();

  foreach (Preinscription::Preinscriptions() as $p) {
    $data[] = array (
      "login" => $p->login(),
      "url" => _GESTION_ .'/?page=preinscription&id='. $p->id()
    );
  }

  $data = json_encode($data);
  exit($data);

}

?>