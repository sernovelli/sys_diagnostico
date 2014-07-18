<?php

require_once 'models/PerfilMenuModel.php';
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - PerfilMenuController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
class PerfilMenuController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "PerfilMenu";
	
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
		$objeto = new PerfilMenuModel();
		
		// Listando os perfis cadastrados
		$vetor = $objeto->_list($_REQUEST['id'], $_REQUEST['perfil'], $_REQUEST['menu'], $_REQUEST['status']);
		
		// definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/'.$this->controle.'View.phtml');
		
		// Passando os dados do perfil para a View
		$o_view->setParams(array('vetor' => $vetor));
		
		// Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	public function manterAction() {
		$objeto = new PerfilMenuModel();
		
		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados
				$objeto->loadById($_REQUEST['id']);
			}
		} 
		
		//echo count($_POST); break;
		if (count($_POST) > 0) {
			//print "perfil: ".$_POST['idPerfil']; break;
			
			$objeto->setPkdoPerfil(DataFilter::cleanString($_POST['idPerfil']));
			$objeto->setPkdoMenu(DataFilter::cleanString($_POST['idmenu']));
			
			//salvando dados e redirecionando para a lista de contatos
			if ($objeto->save() > 0) {
				// Salvou o registro
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=1');
			} else 
			if ($objeto->save() == "") {
				// Atualizou o registro
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=15');
			} else 
			if ($objeto->save() == -1) {
				// Já existe um registro com os mesmos dados
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=16');
			} else {
				// Erro na operação realizada (salvar/editar)
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
			$objeto = new PerfilMenuModel();
			
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