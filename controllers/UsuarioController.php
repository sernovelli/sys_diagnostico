<?php

require_once 'models/UsuarioModel.php';
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - UsuarioController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
class UsuarioController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Usuario";
	
	public function indexAction() {
		// redireciona para a página de login caso o acesso seja
		// feito de forma direta, sem informar a ação na URL.
		header('Location: ?controle=Sair&acao=encerraSessao');
	}
	
	/**
	* Efetua a manipula��o dos modelos necessarios
	* para a aprensenta��o da lista de contatos
	*/	
	public function listarAction() {
		$objeto = new UsuarioModel();
		
		// Listando os perfis cadastrados
		$vetor = $objeto->_list($_REQUEST['id'], $_REQUEST['nome'], $_REQUEST['perfil'], $_REQUEST['status']);
		
		// definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/'.$this->controle.'View.phtml');
		
		// Passando os dados do perfil para a View
		$o_view->setParams(array('vetor' => $vetor));
		
		// Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	public function manterAction() {
		$objeto = new UsuarioModel();
		
		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			}
		} 
		
		//echo count($_POST); break;
		if (count($_POST) > 0) {
			//echo "tem post"; break;
			$objeto->setNome(DataFilter::cleanString($_POST['nome']));
			$objeto->setCargo(DataFilter::cleanString($_POST['cargo']));
			$objeto->setTelefone(DataFilter::cleanString($_POST['telefone']));
			$objeto->setEmail(DataFilter::cleanString($_POST['email']));
			$objeto->setLogin(DataFilter::cleanStringMin($_POST['login']));
			$objeto->setSenha(DataFilter::cleanStringMin($_POST['senha']));
			$objeto->setCpf(DataFilter::cleanString($_POST['cpf']));
			$objeto->setUsuarioStatus(DataFilter::cleanString($_POST['status']));
			$objeto->setIdPerfil(DataFilter::cleanString($_POST['idPerfil']));
			
			//salvando dados e redirecionando para a lista de contatos
			if ($objeto->save() > 0) {
				
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=1');
			 } else 
			if ($objeto->save() == "") {
				// Atualizou o registro
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=15');
			} else {
				// Erro na operação
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=2');
			}
		}
			
		$o_view = new View('views/Manter'.$this->controle.'.phtml');
		$o_view->setParams(array('objeto' => $objeto));
		$o_view->showContents();
	}
	
	public function apagarAction() {
		if (DataValidator::isNumeric($_GET['id'])) {
			
			//apagando o contato
			$objeto = new UsuarioModel();
			
			$objeto->loadById($_GET['id']);
			
			if ($objeto->delete()) {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=3'); // sucesso
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=4');
			}
		}
	}
}
?>