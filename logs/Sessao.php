<?php
class Sessao {
		
	public function formataDados($validaSessao) {
		//echo $validaSessao; break;
		
		if (!isset($_SESSION['validaSessao']) || $_SESSION['validaSessao'] != 1) {
			header('Location: ?controle=Inicio&acao=logar');
			
		} else {
			//texto a ser gravado no log
			$texto = 	" \n *------------------------------------------------------------*
					  \n Id da sessão: ".session_id()."
					  \n Nome da sessão: ".$_SESSION['nomeSessao']."
					  \n Usuário: ".$_SESSION['nomeUsuario']." (".$_SESSION['login'].")
					  \n Tipo de acesso: ".$_SESSION['tipoAutenticacao']." 
					  \n Data: ".date("d/m/Y H:i:s")." 
					  \n	End. IP: ".$_SERVER['REMOTE_ADDR']." 
					  \n	Porta: ".$_SERVER['REMOTE_PORT']." 
					  \n	Método de acesso: ".$_SERVER['REQUEST_METHOD']." 
					  \n	URL da Origem: ".$_SERVER['PHP_SELF']." 
					  \n	Origem do acesso: ".$_SERVER['HTTP_REFERER']." \n";
			 
			// grava log de acessos/trabalho no sistema
			return $this->grava_log($texto);
	    }
	}
	
	public function grava_log($texto) {
	
		// DEFINE O NOME DO ARQUIVO E EXTENSÃO
		$nome_arquivo = "acesso_".date("d-m-Y").".txt";
		
		// CAMINHO ONDE SERÁ GRAVADO O ARQUIVO
		$arquivo = "logs/acessos/".$nome_arquivo;
	
		//TENTA ABRIR O ARQUIVO TXT
		if (file_exists($nome_arquivo)) {
			if (!$abrir = fopen($arquivo, "w")) {
				return "Erro abrindo arquivo ($arquivo) pelo metodo [w]";
			}
		} else {
			if (!$abrir = fopen($arquivo, "a")) {
				return "Erro abrindo arquivo ($arquivo) pelo metodo [a]";
			}
		}
		
		//$texto = "\\\\------------------- ".$texto." //-------------------////";
		
		// TENTA ESCREVER NO ARQUIVO TXT
		if (!fwrite($abrir, $texto)) {
			return "Erro escrevendo no arquivo ($arquivo)";
		}
		
		// FECHA O ARQUIVO INDEPENDENTE DE TER CONSEGUIDO ESCREVER OU NÃO O ARQUIVO.
		fclose($abrir);
	
		/* Informações sobre variaveis globais:
			PHP_SELF			Nome do arquivo do script atualmente em uso.
			SERVER_NAME 		Nome do servidor onde o script atual é executado.
			SERVER_SOFTWARE 	String de identificação do servidor.
			SERVER_PORT 		Porta usada pelo servidor para comunicação.
			REQUEST_METHOD 		Método utilizado para acessar a página. Exemplo: GET, HEAD, POST, PUT.
			QUERY_STRING 		String de solicitação pela qual a página foi solicitada.
			DOCUMENT_ROOT 		Diretório raiz onde o script atual é executado.
			HTTP_REFERER 		Endereço da página pelo qual o usuário acessou a página atual.
			HTTP_USER_AGENT 	Browser utilizado pelo usuário. Pode-se usar a função get_browser() também.
			REMOTE_ADDR 		Endereço IP do usuário.
			REMOTE_PORT 		Porta TCP utilizada pelo usuário para comunicação com o servidor.
			SCRIPT_FILENAME 	Caminho absoluto do script atual em execução.
			SCRIPT_NAME 		Caminho completo do script atual.
		*/
	}
}
?>