<?php
//ob_start();
//session_start();

//$urlSistema = $_SESSION['url']."index2.php";

class SairController {
		
	public function encerraSessaoAction() {
		ob_start();
		session_start();
		
		if (isset($_SESSION['validaSessao']) && $_SESSION['validaSessao'] == 1) {
			//echo $_SESSION['nomeSessao']." - ";
			
			// primeiro destroi os dados associados à sessão
			$_SESSION = array();
		
			// destroi os cookies criados com esta sessao
	     	setcookie(session_name(), '', time() - 1000, '/');
			
			if (session_destroy()) {
				//echo $_SESSION['nomeSessao']; break;				
				header('Location: ?controle=Inicio&acao=logar');
			}
		}
	}
}


?>