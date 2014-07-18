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
 
class Mnvertical {
	
	/**
	* Efetua a manipula��o dos modelos necessarios
	* para a aprensenta��o da lista de contatos
	*/	
	public function listarAction($perfil) {

		$objeto = new MenuModel();
		
		//Listando os registros e guardando em uma lista
		$vetor = $objeto->menuVertical($perfil);
		return $vetor;
	}
}
?>