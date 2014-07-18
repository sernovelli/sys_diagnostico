<?php

/**
* @package Exemplo simples com MVC
* @author DigitalDev
* @version 0.1.0
* 
* Cam�da - Controladores ou Controllers
* Diret�rio Pai - controllers 
* 
* Controlador que dever� ser chamado quando n�o for
* especificado nenhum outro
*/
class IndexController
{
	/**
	* Ação que deverá ser executada quando 
	* nenhuma outra for especificada, do mesmo jeito que o
	* arquivo index.html ou index.php � executado quando nenhum é
	* referenciado
	*/
	public function indexAction()
	{
		//redirecionando para a pagina de login ou inicial do sistema
		//header('Location: ?controle=Inicio&acao=logar');
		//header('Location: ?controle=Inicio&acao=dashboard');
		Application::redirect('?controle=Inicio&acao=dashboard');
	}
}
?>