<?php
$nomeRemetente = $_POST['name'];
$emailRemetente = $_POST['email'];
$msgRemetente = $_POST['message'];
// Verifica se o nome foi preenchido
if (empty($nomeRemetente)) {
	echo "Escreva seu nome";
}
elseif ($nomeRemetente=="Nome:") {
	echo "Escreva seu nome";
} 
// Verifica se o email é válido
elseif (!preg_match("/^[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\-]+\.[a-z]{2,4}$/i", $emailRemetente)) {
	echo "Digite um email válido";
}
elseif ($emailRemetente=="E-mail:") {
	echo "Escreva seu e-mail";
} 
// Verifica se o nome foi preenchido
elseif (empty($msgRemetente)) {
	echo "Digite a sua mensagem";
} 
elseif ($msgRemetente=="Sua mensagem:") {
	echo "Digite a sua mensagem";
} 
// Se não houver nenhum erro
else {
	
	$assunto = "DIAGNOSTIC - PROBLEMAS DE ACESSO";
	$destino = "sergio@alkantara.com.br";
	$headers = "From: ".$nomeRemetente ." <".$emailRemetente.">\nContent-type: text/html; charset=iso-8859-1\n";
	// Montando o email
	$mensagem = "Veja abaixo o contato recebido pelo sistema Diagnostic: <br><br>";
	$mensagem .= "Enviado por: <b>".$nomeRemetente."</b><br>";
	$mensagem .= "E-mail de contato: <b>".$emailRemetente."</b><br><br>";
	$mensagem .= "<b>Mensagem enviada:</b> <br>";
	$mensagem .= $msgRemetente."<br>";
	
	// fazendo o envio do email.
	$email = mail($destino, $assunto, $mensagem, $headers);
	
	// verifica se o email foi enviado.
	if ($email) {
		echo false;
	} else {
		echo "Não foi possível enviar seu e-mail.";
	}
}
?>