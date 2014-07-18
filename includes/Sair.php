<?php
//ob_start();
//session_start();

//$urlSistema = $_SESSION['url']."index2.php";

class Sair {
	
	private $sessaoId;
	private $sessaoNome;
	
	public function __construct($idSessao,$nomeSessao) {
		$this->setSessaoId($idSessao);
		$this->setSessaoNome($nomeSessao);
	}
	
	
	public function getSessaoId() {
		return $this->sessaoId;
	}
	
	public function setSessaoId($sessaoId) {
		$this->sessaoId = $sessaoId;
		return $this;
	}
	
	public function getSessaoNome() {
		return $this->sessaoNome;
	}
	
	public function setSessaoNome($sessaoNome) {
		$this->sessaoNome = $sessaoNome;
		return $this;
	}
	
	public function encerraSessao() {
			
  		if ($this->getSessaoId() != "" || $this->getSessaoId() != null) {
			// primeiro destroi os dados associados à sessão
  			$_SESSION = array();
			
			// destroi os cookies criados com esta sessao
			if (isset($_COOKIE[session_name()])){
			    // setcookie(session_name(), '', time() - 1000, '/');
			    setcookie($this->getSessaoNome(), '', time() - 1000, '/');
			} 
			
			if (session_destroy()) {
				//redirecionando para a pagina de login ou inicial do sistema
				header('Location: ?controle=Inicio&acao=logar');
			}
		}
	}
}


?>