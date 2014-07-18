<?php
// inicia a sessao
ob_start();	
session_start();

require_once 'models/UsuarioModel.php';
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - SuporteController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
class SuporteController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Suporte";
	
	public function indexAction() {
		// redireciona para a página de login caso o acesso seja
		// feito de forma direta, sem informar a ação na URL.
		header('Location: ?controle=Sair&acao=encerraSessao');
	}
	
	public function manterAction() {
		$objeto = new UsuarioModel();
		
		if (isset($_REQUEST['idFilial'])) {
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['idFilial'])) {
				
				if ($_SESSION['hashid'] == "cliente") {
					/**
					 * Traz os dados do gerente da filial para o formulário de suporte
					 * quando um cliente está logado e acessa o suporte.
					 */
					$objeto->loadByIdFilial($_REQUEST['idFilial']);

				} else 
				if ($_SESSION['hashid'] == "gerentefilial") {
					/**
					 * Traz os dados do super administrador para o formulário de suporte
					 * quando um gerente de filial está logado e acessa o suporte.
					 */
					$objeto->loadSuperAdmin();
				}
			}
		}
		
		if ($_SESSION['hashid'] == "superadmin") {
			// Direciona para a página de suporte técnico da Alkantára.
			Application::redirect('?controle=Inicio&acao=suporte');
		}
		
		if (count($_POST) > 0) {
			
			$nomeDestino = 	DataFilter::cleanString($_POST['nomeGerente']);
			$emailDestino = 	DataFilter::cleanString($_POST['emailGerente']);
			$assunto = 		DataFilter::cleanString($_POST['assunto']);
			$nomeRemetente = 	DataFilter::cleanString($_POST['remetente']);
			$emailRemetente = 	DataFilter::cleanString($_POST['emailRemetente']);
			$telRemetente = 	DataFilter::cleanString($_POST['telRemetente']);
			$filial =		 	DataFilter::cleanString($_POST['filial']);
			$mensagem = 		DataFilter::cleanString($_POST['msgSuporte']);
			
			if ($_SESSION['hashid'] == "cliente") {
				if ($this->enviaEmail($assunto, $nomeRemetente, $emailRemetente, $telRemetente, $filial, $mensagem, $nomeDestino, $emailDestino)) {
					Application::redirect('?controle=Inicio&acao=dashboard&msg=38');
				} else {
					Application::redirect('?controle='.$this->controle.'&acao=manter&msg=37');
				}
			} else 
			if ($_SESSION['hashid'] == "gerentefilial") {
				if ($this->enviaEmailSuperAdmin($assunto, $nomeRemetente, $emailRemetente, $telRemetente, $filial, $mensagem, $nomeDestino, $emailDestino)) {
					Application::redirect('?controle=Inicio&acao=dashboard&msg=38');
				} else {
					Application::redirect('?controle='.$this->controle.'&acao=manter&msg=37');
				}
			}
		}
		
		$o_view = new View('views/Manter'.$this->controle.'.phtml');
		$o_view->setParams(array('objeto' => $objeto));
		$o_view->showContents();
	}

	/**
	 * Função para envio de email para o gerente da filial.
	 */
	private function enviaEmail($assunto, $nomeRemetente, $emailRemetente, $telRemetente, $filial, $mensagem, $nomeDestino, $emailDestino) {

		// Prepara envio do e-mail para o gerente da filial na qual o cliente está vinculado:
		$headers = "From: ".$nomeRemetente ." <".$emailRemetente.">\nContent-type: text/html; charset=iso-8859-1\n";
		// $headers .= "\nMIME-Version: 1.0\r\n";
		// $headers .= "\nContent-type: text/html; charset=iso-8859-1\n";
		
		$msg = "Sr. ".$nomeDestino."<br /><br />";
		$msg .= "O sr. ".$nomeRemetente.", vinculado à ".$filial.", solicitou suporte para o sistema de gerenciamento de arquivos - Diagnostic.<br /><br />";
		$msg .= "Abaixo está a sua mensagem:<br /><br />";
		
		$msg .= "<strong>".$mensagem."</strong><br /><br />";
		
		$msg .= "Você pode entrar em contato com o sr. ".$nomeRemetente." através do telefone: ".$telRemetente." ou pelo email: ".$emailRemetente."<br /><br />";
		$msg .= "Sistema Diagnostic";
		
		// faz o envio
		$email = mail("$emailDestino", "$assunto", "$msg", "$headers");

		//print "assunto: ".$assunto." remetente: ".$nomeRemetente." emailRem: ".$emailRemetente." telRem: ".$telRemetente." filial: ".$filial." mensagem: ".$mensagem." destino: ".$nomeDestino." emaiDest: ".$emailDestino; break;
		
		if ($email) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Função para envio de email para o super administrador.
	 */
	private function enviaEmailSuperAdmin($assunto, $nomeRemetente, $emailRemetente, $telRemetente, $filial, $mensagem, $nomeDestino, $emailDestino) {

		// Prepara envio do e-mail para o gerente da filial na qual o cliente está vinculado:
		$headers = "From: ".$nomeRemetente ." <".$emailRemetente.">\nContent-type: text/html; charset=iso-8859-1\n";
		// $headers .= "\nMIME-Version: 1.0\r\n";
		// $headers .= "\nContent-type: text/html; charset=iso-8859-1\n";
		
		$msg = "Sr. ".$nomeDestino."<br /><br />";
		$msg .= "O gerente da filial ".$filial." lhe solicitou suporte para o sistema de gerenciamento de arquivos - Diagnostic.<br /><br />";
		$msg .= "Leia abaixo a mensagem que ele enviou:<br /><br />";
		
		$msg .= "<strong>".$mensagem."</strong><br /><br />";
		
		$msg .= "Você pode entrar em contato com o sr. ".$nomeRemetente." através do telefone: ".$telRemetente." ou pelo email: ".$emailRemetente."<br /><br />";
		$msg .= "Sistema Diagnostic";
		
		// faz o envio
		$email = mail("$emailDestino", "$assunto", "$msg", "$headers");
		
		if ($email) {
			return true;
		} else {
			return false;
		}
	}
}
?>