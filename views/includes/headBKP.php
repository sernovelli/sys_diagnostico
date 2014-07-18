<?php
/**
 * GERAARQUIVOS - Sistema gerenciador de arquivos
 * @Versao: 1.0 - 2013
 * @autor: Sérgio Novelli - sergio@alkantara.com.br
 * @agencia: Alkantára
 * @cliente: Dinamica RH & Merchandising
 */
 
 ob_start();
 session_start();
 
 // Grava logs de acesso e operações
 // do usuário logado
 include_once 'logs/Sessao.php';
 $log = new Sessao();
 $log->formataDados($_SESSION['validaSessao']);
 
 date_default_timezone_set('America/Sao_Paulo');
?>

<!doctype html>
<html lang="pt-BR" dir="ltr">
<head>
	<meta charset="UTF-8">
	<link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
	<meta name="description" content="Sistema de gerenciamento de PDV - Dinâmica RH Merchandising">
	<meta name="keywords" content="dinamica, merchandising, pdv, arquivos, fotos">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>DIAGNOSTIC - Sistema Gerenciador de Arquivos - Dinamica RH & Merchandising</title>
	<link rel="stylesheet" href="views/includes/css/default.css" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	
<?php $controle = $_GET['controle']; 
	if ($controle == "Foto" || $controle == "Inicio") { ?>
	<script src="views/includes/lightbox/js/lightbox-2.6.min.js"></script>
	<link href="views/includes/lightbox/css/lightbox.css" rel="stylesheet" />
<?php } ?>
	
	<script type="text/javascript" src="views/includes/js/bpopup.js"></script>
     <script type="text/javascript" src="views/includes/js/maskedinput.js"></script>
	<script>
    	<?php if ($_GET['msg'] != "") { ?>
        // Script que esconde a mensagem apos X segundos
        window.setInterval('esconde()', 30000); // 5000 = 5 seg.
        function esconde() {
            document.getElementById('mensagem').style.display = 'none';
        }
     <?php } ?>
        // Menu Topo
        $(document).ready(function(){
        	$('li.config').click(function(){
        		$(this).parent().find('ul').slideToggle("slow");
        		$(this).toggleClass("hover");
        		$(this).children().toggleClass("open");
        	});
        });
        $(document).ready(function(){
            $('form').addClass('formee');
        });
    </script>