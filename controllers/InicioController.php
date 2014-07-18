<?php
// inicia a sessao
ob_start();	
session_start();

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
 
 //incluindo classes da camada Model
require_once 'models/InicioModel.php';
 
class InicioController {
		
	public function logarAction() {
		//direciona para a página de login do sistema
		$o_view = new View('views/index.php');
		
		//Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	/**
	 * Método para abrir o painel inicial quando 
	 * o usuario usa o link 'Inicio' do menu de navegação.
	 */
	public function dashboardAction() {
		// cria o objeto.
		$usuario = new InicioModel();
		
		// Lista os dados do usuário logado.
		$vetor = $usuario->_list($_SESSION['idUsuario']);
		
		//definindo qual o arquivo HTML que sera usado para ser exibido.
		$o_view = new View('views/InicioView.phtml');
		
		// Passando os dados para a view.
		$o_view->setParams(array('vetor' => $vetor));
		
		//Imprimindo codigo HTML.
		$o_view->showContents();

	}
	
	/**
	 * Método para abrir a página de suporte para o super administrador
	 * entrar em cntato com a alkantára
	 */
	public function suporteAction() {
		
		//definindo qual o arquivo HTML que sera usado para ser exibido.
		$o_view = new View('views/SuporteAlkantara.phtml');
		
		//Imprimindo codigo HTML.
		$o_view->showContents();

	}
}
?>