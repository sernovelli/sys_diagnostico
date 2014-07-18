<?php
/**
 * GERAARQUIVOS - Sistema gerenciador de arquivos
 * @Versao: 1.0 - 2013
 * @autor: Sérgio Novelli - sergio@alkantara.com.br
 * @agencia: Alkantára
 * @cliente: Dinamica RH & Merchandising
 */
 
 // ob_start();
 // session_start();
 
$v_params = $this->getParams();
$vetor = $v_params['vetor'];
?>
<!doctype html>
<html lang="pt-BR" dir="ltr">
	<head>
		<meta charset="UTF-8">
		<title>DIAGNOSTIC - Sistema Gerenciador de Arquivos - Dinamica RH & Merchandising</title>
	</head>
	<body>
		<h1>Automa&ccedil;&atilde;o de Recursos - Publica&ccedil;&atilde;o Autom&aacute;tica</h1>
		
		<a href="?controle=Automacao&acao=desativarFotos">Despublicar Fotos</a> - Desativa as fotos publicadas a mais de 90 dias.<br /><br />
		
		<a href="?controle=Automacao&acao=excluirFotos">Excluir Fotos</a> - Exclui as fotos publicadas a mais de 180 dias.<br /><br /> 
		
		<a href="?controle=Automacao&acao=desativarArquivos">Despublicar Arquivos</a> - Desativa os arquivos (relat&oacute;rios) publicados a mais de 90 dias.<br /><br />
		
		<a href="?controle=Automacao&acao=excluirArquivos">Excluir Arquivos</a> - Desativa os arquivos (relat&oacute;rios) publicados a mais de 180 dias.<br /><br />
		
		<hr /> 
		
		<h1>Automa&ccedil;&atilde;o de Recursos - Notifica&ccedil;&atilde;o Autom&aacute;tica</h1>
		
		<a href="?controle=Automacao&acao=notificarFotos">Notificar Fotos</a> - Envia e-mail avisando os clientes sobre novas publica&ccedil;&otilde;es de fotos.<br /><br />
		
		<a href="?controle=Automacao&acao=notificarArquivos">Notificar Arquivos</a> - Envia e-mail avisando os clientes sobre novas publica&ccedil;&otilde;es de arquivos (relat&oacute;rios).<br /><br />
		
		<hr />
		
	<?php 
		if (count($vetor) > 1) {
			echo "Ocorreu(ram) ( ".$vetor['erros']." ) erro(s) no processo.<br />";
			echo "( ".$vetor['alteracoes']." ) registro(s) foi(ram) alterado(s) com sucesso!";
		} else if (count($vetor) == 1) {
			echo $vetor['resultado'];
		}
	?>
		<hr />
	</body>
</html>

