<?php

include_once '../../controllers/LoginController.php';
require_once '../../lib/Application.php';

if (isset($_REQUEST)) {
	$logando = new LoginController();
	$logando->setUsuario($_REQUEST['usuario']);
	$logando->setSenha($_REQUEST['senha']);
	$logando->setTipo($_REQUEST['tipo']);
	
	header('Location: ../../controllers/LoginController.php');
}

?>