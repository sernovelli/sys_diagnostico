<?php

require_once 'models/MenuModel.php';
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - InicioController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
class MenuController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Menu";
	
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
		$objeto = new MenuModel();
		
		//Listando os registros e guardando em uma lista
		$vetor = $objeto->_list($_REQUEST['id'], $_REQUEST['menu']);
		
		//definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/MenuView.phtml');
		
		//Passando os dados do objeto para a View
		$o_view->setParams(array('vetor' => $vetor));
		
		//Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	public function manterAction() {
		$objeto = new MenuModel();
		
		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado eh valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=5');
			}
		} 

		if (count($_POST) > 0) {
			$objeto->setMenu(DataFilter::cleanStringMin($_POST['menu']));
			$objeto->setUrl(DataFilter::cleanStringMin($_POST['url']));
			$objeto->setMenuAtivo(DataFilter::cleanStringMin($_POST['menuAtivo']));
			$objeto->setOrdem(DataFilter::cleanStringMin($_POST['ordem']));
			$objeto->setMenuPai(DataFilter::cleanStringMin($_POST['menuPai']));
			
			//salvando dados e redirecionando para a listagem de registros
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
			
		$o_view = new View('views/ManterMenu.phtml');
		$o_view->setParams(array('objeto' => $objeto));
		$o_view->showContents();
	}
	
	public function apagarAction() {
		if (DataValidator::isNumeric($_GET['id'])) {
			//apagando o contato
			$objeto = new MenuModel();
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