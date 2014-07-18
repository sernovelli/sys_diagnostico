<?php

// inicia a sessao
ob_start();
session_start();

//incluindo classes da camada Model
require_once 'models/AutenticarModel.php';
require_once 'models/InicioModel.php';

/**
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - LoginController.php
 * 
 * @author Sérgio Novelli
 * @version 1.0
 *
 */
class LoginController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Inicio";
	
	// id do usuario logado
	private $idUsuarioLogado;
	
	/**
	* Efetua a manipulacao dos modelos necessarios
	* para a aprensentacao da pagina inicial
	*/
	public function logarAction() {
		$o_login = new AutenticarModel();
		
		//verificando se o usuario do login foi passado
		if (isset($_REQUEST['usuario']) && isset($_REQUEST['tipo'])) {
				
			if ($_REQUEST['tipo'] == 1) {
				//buscando dados do usuario conforme usuario informado no form
				$o_login->loadByUsuarioFilial($_REQUEST['usuario']);
				$tipoLogin = "Filial/Dinamica";
			} 
			else	if ($_REQUEST['tipo'] == 2) {
				//buscando dados do usuario conforme usuario informado no form
				$o_login->loadByUsuarioCliente($_REQUEST['usuario']);
				$tipoLogin = "Cliente";
			} else {
				// tipo não selecionado '0'
				header('Location: ?controle='.$this->controle.'&acao=logar&msg=14');
				break;
			}
		
			if ($this->verificaUsuario($o_login->getLogin(), DataFilter::cleanStringMin($_REQUEST['usuario']))) {
				
				if ($this->verificaSenha($o_login->getSenha(), DataFilter::cleanStringMin($_REQUEST['senha']))) {
						
					if ($this->verificaStatus($o_login->getUsuarioStatus())) {
							
						if ($this->verificaPerfil($o_login->getPerfil())) {
							
							// configura o nome da sessao com o login do usuario
							$_SESSION['nomeSessao'] = session_name()."_".strtoupper($o_login->getLogin());
							
							// usuario
							$_SESSION['idUsuario'] = $o_login->getPk(); // vem do AutenticarModel.php
							$_SESSION['nomeUsuario'] = $o_login->getNome();
							$_SESSION['login'] = $o_login->getLogin();
							$_SESSION['email'] = $o_login->getEmail();
							$_SESSION['telefone'] = $o_login->getTelefone();
							// perfil
							$_SESSION['idPerfilUsuario'] = $o_login->getPerfil();
							$_SESSION['nomePerfil'] = $o_login->getNomePerfil();
							$_SESSION['hashid'] = $o_login->getHashid();
							$_SESSION['adicionar'] = $o_login->getAdicionar();
							$_SESSION['editar'] = $o_login->getEditar();
							$_SESSION['excluir'] = $o_login->getExcluir();
							
							// cliente - apenas se logado como cliente
							if ($_REQUEST['tipo'] == 2) {
								$_SESSION['idCliente'] = $o_login->getPkCliente();
							}
							
							// filial
							$_SESSION['filial'] = $o_login->getFilial();
							$_SESSION['idFilial'] = $o_login->getIdFilial();

							// valida a sessão
							$_SESSION['validaSessao'] = 1;
							
							// pega o tipo de autenticacao: filial/dinamica ou cliente
							$_SESSION['tipoAutenticacao'] = $tipoLogin;
							
							$this->idUsuarioLogado = $o_login->getPk();
							
							$this->dashboard();
							
						} else {
							// Perfil não identificado
							header('Location: ?controle='.$this->controle.'&acao=logar&msg=13');
							break;
						}
					} else {
						// Usuario inativo
						header('Location: ?controle='.$this->controle.'&acao=logar&msg=12');
						break;
					}
				} else {
					// Senha incorreta
					header('Location: ?controle='.$this->controle.'&acao=logar&msg=11');
					break;
				}
			} else {
				// usuário incorreto
				header('Location: ?controle='.$this->controle.'&acao=logar&msg=10');
				break;
			}
		} else {
			header('Location: ?controle='.$this->controle.'&acao=logar&msg=14');
			break;
		}
	}
	
	/**
	 * Método para abrir o painel inicial quando o
	 * usuário faz a autenticação no sistema.
	 * 
	 * Cria um objeto que busca todos os dados necessários para 
	 * apresentar no painel inicial.
	 */
	public function dashboard() {
		// cria o objeto da dashboard.
		$usuario = new InicioModel();
		
		//print "idUsuario: ".$_SESSION['idUsuario'];
		
		// Lista os dados do usuário logado.
		$vetor = $usuario->_list($this->idUsuarioLogado);
		
		//definindo qual o arquivo HTML que sera usado para ser exibido.
		$o_view = new View('views/InicioView.phtml');
		
		// Passando os dados para a view.
		$o_view->setParams(array('vetor' => $vetor));
		
		//Imprimindo codigo HTML.
		$o_view->showContents();
		
		Application::redirect('?controle='.$this->controle.'&acao=dashboard');
	}
	
	// Autenticação do usuário
	private function verificaUsuario($loginBD,$loginForm) {
		if ($loginBD == $loginForm) {
			return true;
		}
		return false;
	}
	
	private function verificaSenha($senhaBD,$senhaForm) {
		if (md5($senhaBD) == md5($senhaForm)) {
			return true;
		}
		return false;
	}
	
	private function verificaStatus($usuarioStatus) {
		if ($usuarioStatus = 1) {
			return true;
		}
		return false;
	}
	
	private function verificaPerfil($perfil) {
		if ($perfil != 0 && $perfil != "") {
			return true;
		}
		return false;
	}
}
?>