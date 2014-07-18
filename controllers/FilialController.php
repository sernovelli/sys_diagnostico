<?php

require_once 'models/FilialModel.php';
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - CidadeController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
class FilialController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Filial";
	
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
		$objeto = new FilialModel();
		
		//Listando os perfis cadastrados
		$vetor = $objeto->_list();
		
		//definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/'.$this->controle.'View.phtml');
		
		//Passando os dados do perfil para a View
		$o_view->setParams(array('vetor' => $vetor));
		
		//Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	public function manterAction() {
		$objeto = new FilialModel();
		
		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado � valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			}
		} 
		
		//echo count($_POST); break;
		if (count($_POST) > 0) {
			//echo "tem post"; break;
			$objeto->setFilial(DataFilter::cleanString($_POST['filial']));
			$objeto->setIdCidade(DataFilter::cleanString($_POST['idCidade']));
			//$objeto->setNomeCidade(DataFilter::cleanString($_POST['nCidade']));

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
			$objeto = new FilialModel();
			
			$objeto->loadById($_GET['id']);
			
			if ($objeto->delete()) {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=3'); // sucesso
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=4');
			}
		}
	}
	
	 /**
	 * Método para atualizar o cadastro da filial do gerente logado no sistema.
	 * É chamado ao clicar no botão 'Salvar' do formulário.
	 */
	public function updateAction() {
		$objeto = new FilialModel();
		
		//verificando se o id foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado � valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			}
		}
		
		if (count($_POST) > 0) {
			
			$objeto->setFilial(DataFilter::cleanString($_POST['filial']));
			$objeto->setIdCidade(DataFilter::cleanString($_POST['idCidade']));
			
			//salvando dados e redirecionando para a lista de contatos
			if ($objeto->save() > 0) {
				Application::redirect('?controle=Inicio&acao=dashboard&msg=1');
			} else 
			if ($objeto->save() == "") {
				// Atualizou o registro
				Application::redirect('?controle='.$this->controle.'&acao=manter&msg=15');
			} else {
				// Erro na operação
				Application::redirect('?controle='.$this->controle.'&acao=manter&msg=2');
			}
		}
	}
}
?>