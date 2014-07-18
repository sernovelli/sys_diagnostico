<?php 
/**
 * DIAGNOSTIC - Sistema gerenciador de arquivos
 * @Versao: 1.0 - 2013
 * @equipe: Sérgio Novelli - Analista/desenvolvedor web - sergio@alkantara.com.br
 * 		  Nelson Júnior - Programador - nelson@alkantara.com.br
 * 		  Douglas Figueredo - webdesigner - douglas@alkantara.com.br
 * 		  Milton de Campos - Gerente Comercial
 * @agencia: Alkantára
 * @cliente: Dinamica RH & Merchandising
 */
?>
<!doctype html>
<html lang="pt-BR" dir="ltr">
<head>
    <meta charset="UTF-8">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
    <meta name="description" content="Sistema de gerenciamento de PDV - Dinâmica RH Merchandising">
    <meta name="keywords" content="dinamica, merchandising, pdv, arquivos, fotos">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="views/includes/css/default.css" />
	<script type="text/javascript" src="views/includes/js/jquery-1.10.1.min.js"></script>
	<script type="text/javascript" src="views/includes/js/bpopup.js"></script>
	<title>DIAGNOSTIC - Sistema Gerenciador de Arquivos - Dinamica RH & Merchandising</title>
	<!-- Script que esconde a mensagem apos X segundos -->
	<script>
		<?php if ($_GET['msg'] != "") { ?>
			window.setInterval('esconde()', 5000);
			  function esconde() {
				document.getElementById('mensagem').style.display = 'none';
			  }
		<?php } ?>
		// POP FORMULÁRIO
		$(document).ready(function(){
		  $(function() {
			$('#linkform').bind('click', function(e) {
			  e.preventDefault();
			  $('#formpop').bPopup();
			});
		  });
		});
		// ENVIO DO FORMULÁRIO
		$(document).ready(function(){
			$(function($) {
				// Quando o formulário for enviado, essa função é chamada
				$("#contato").submit(function() {
					// Colocamos os valores de cada campo em uma váriavel para facilitar a manipulação
					var name = $("#name").val();
					var email = $("#email").val();
					var message = $("#message").val();
					// Exibe mensagem de carregamento
					$("#status").html("<img src='views/includes/images/loader.gif' alt='Enviando' />");
					// Fazemos a requisão ajax com o arquivo envia.php e enviamos os valores de cada campo através do método POST
					$.post('views/includes/envia.php', {name: name, email: email, message: message }, function(resposta) {
							// Quando terminada a requisição
							// Exibe a div status
							$("#status").slideDown();
							// Se a resposta é um erro
							if (resposta != false) {
								// Exibe o erro na div
								$("#status").html(resposta);
							}
							// Se resposta for false, ou seja, não ocorreu nenhum erro
							else {
								// Exibe mensagem de sucesso
								$("#status").html("Enviado com sucesso!");
								// Limpando todos os campos
								$("#name").val("Nome:");
								$("#email").val("E-mail:");
								$("#message").val("Sua mensagem:");
							}
					});
				});
			});
		});
    </script>
</head>
<body class="login">
    <article>
    	<header><h1>Diagnostic</h1></header>
        <section>
        	<p>Acesse a área restrita da Dinâmica RH Merchandising e acompanhe o seu PDV:</p>
            <?php if (isset($_GET['msg']) != "") { ?>
             <div id="mensagem" style="display:block;">
                <?php 
                    include_once 'lib/Mensagens.php';
                    $msg = new Mensagens($_GET['msg']);
                ?>
             </div>
            <?php } ?>
            <form name="login" method="post">
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo">
                    <option value="0">Selecione</option>
                    <option value="1" selected="selected">Filial/Dinâmica</option>
                    <option value="2">Cliente</option>
                </select>
            	<div class="clear"></div>
                <label for="usuario">Usuário:</label>
                <input type="text" name="usuario" id="usuario" value="" />
                <a href="">Lembrar login</a>
            	<div class="clear"></div>
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" value="" />
                <a href="">Lembrar senha</a>
            	<div class="clear"></div>
                <input type='hidden' name='controle' value='Login'>
                <input type='hidden' name='acao' value='logar'>
                <input type="submit" value="Acessar »" />
            </form>
            <div class="clear"></div>
        </section>
        <footer>
            <a class="alkantara" href="http://www.alkantara.com.br" target="_blank">Desenvolvido por Alkantára</a>
            <a id="linkform" class="problema" href="#">Problemas no acesso?</a>
            <span>Diagnostic v1.0 - Todos os direitos reservados. &copy; <?php echo date("Y"); ?></span>
            <div id="formpop">
            	<a class="close" href="">Fechar [x]</a>
                <h3>Problemas no acesso?</h3>
                <p>Envie-nos uma mensagem explicando sua dificuldade:</p>
          		<div id="status" style="display: none;"></div>
                <form id="contato" action="javascript:func()" method="post">
                    <input id="name" name="name" type="text" value="Nome:" onBlur="if(this.value == '') { this.value='Nome:'}" onFocus="if (this.value == 'Nome:') {this.value=''}" />
                    <input id="email" name="email" type="text" value="E-mail:" onBlur="if(this.value == '') { this.value='E-mail:'}" onFocus="if (this.value == 'E-mail:') {this.value=''}" />
                    <textarea id="message" name="message" cols="" rows="" onBlur="if(this.value== '') {this.value='Sua mensagem:'};" onFocus="if(this.value== 'Sua mensagem:'){this.value=''};">Sua mensagem:</textarea>
                    <input id="send" name="send" type="submit" value="Enviar" />
          		</form>
            </div>
        </footer>
	</article>
</body></html>