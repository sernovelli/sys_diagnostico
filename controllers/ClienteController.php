<?php

require_once 'models/ClienteModel.php';
require_once 'models/CidadeModel.php';
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
 
class ClienteController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Cliente";
	
	// var's que recebe o nome da pasta a criar e 
	// o caminho em que a pasta será criada
	private $nomePasta = "";
	// NÃO ESQUECER A '/' NO FINAL DA PATH
	private $caminhoPasta = "arquivos/"; //"D:/wamp/www/dinamicarh/geraarquivos/arquivos/";
	
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
		$objeto = new ClienteModel();
		
		//Listando os perfis cadastrados
		$vetor = $objeto->_list($_REQUEST['id'], $_REQUEST['status'], $_REQUEST['nome'], $_REQUEST['cnpj'], $_REQUEST['contrato']); // $status = 1, $nFantasia = "", $cnpj = "", $contrato = ""
		
		//definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/'.$this->controle.'View.phtml');
		
		//Passando os dados do perfil para a View
		$o_view->setParams(array('vetor' => $vetor));
		
		//Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	public function manterAction() {
		$objeto = new ClienteModel();
		
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
			
			/**
			 * Se NÃO enviou o 'id' (acao = adicionar), então
			 * valida os campos nomeFantasia e o no. contrato do cliente para gerar o nome da pasta.
			 * Se esses campos não forem vazios e nem nulos, gera o nome e cria a pasta no 
			 * caminho especificado.
			 * Se a pasta for criada com sucesso, setta os campos para gravar em BD.
			 * 
			 * Se ENVIOU o 'id' (acao = editar), então não cria a pasta,
			 * pois essa deve permanecer inalterada e ser única para cada cliente.
			 */
			//print $_REQUEST['id']; break;
			
			if (!isset($_REQUEST['id']) || $_REQUEST['id'] == "" || is_null($_REQUEST['id'])) { // se não tem id
				
				if ($this->validaDados($_POST['contrato'], $_POST['nFantasia'])) { // se tem 'contrato' e 'nomeFantasia'
					
					if (!$this->criaPastaPrincipal()) { // se não criou a pasta, volta com erro
						Application::redirect('?controle='.$this->controle.'&acao=listar&msg=17');
					} else { // senão, setta o nome da pasta para gravar no BD.
						$pasta = $this->caminhoPasta.$this->nomePasta;
						$objeto->setNomePasta($pasta);
					}
					
				} else {
					Application::redirect('?controle='.$this->controle.'&acao=listar&msg=18');
				}
			}

			$objeto->setContrato(DataFilter::cleanString($_POST['contrato']));
			$objeto->setNomeFantasia(DataFilter::cleanString($_POST['nFantasia']));
			$objeto->setRazaoSocial(DataFilter::cleanString($_POST['rSocial']));
			$objeto->setCnpj(DataFilter::cleanString($_POST['cnpj']));
			$objeto->setIdCidade(DataFilter::cleanString($_POST['idCidade']));
			$objeto->setClienteStatus(DataFilter::cleanString($_POST['status']));
			$objeto->setNotificaFotos(DataFilter::cleanString(2));
			$objeto->setNotificaArquivos(DataFilter::cleanString(2));
			
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
	
	/**
	 * Valida os dados para gerar o nome da pasta que será criada.
	 * Gera o nome da pasta concatenando o no. contrato e
	 * o nome fantasia informados na view.
	 */
	private function validaDados($contrato,$nomeFantasia) {
		if (!is_null($nomeFantasia) && $nomeFantasia != "") {
			$nome = explode(" ", $nomeFantasia);
			$this->nomePasta = $this->trataTxt($nome[0]);
		} else {
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=21');
		}
		
		if (!is_null($contrato) && $contrato != "") {
			$contr = str_replace('.','',$contrato);
			$contr = str_replace('-','',$contr);
			
			$this->nomePasta = $this->nomePasta ."_".$contr;
		} else {
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=22');
		}
		return true;
	}
	
	/**
	 * Retira a acentuação do nome da pasta.
	 */
	private function trataTxt($var) {
		$var = strtolower($var);
		
		$var = str_replace("á", "a", $var);
		$var = str_replace("à", "a", $var);
		$var = str_replace("â", "a", $var);
		$var = str_replace("ã", "a", $var);
		$var = str_replace("ª", "a", $var);
		
		$var = str_replace("é", "e", $var);
		$var = str_replace("è", "e", $var);
		$var = str_replace("ê", "e", $var);
		//$var = str_replace("", "e", $var);
		
		$var = str_replace("í", "i", $var);
		$var = str_replace("ì", "i", $var);
		$var = str_replace("î", "i", $var);
		//$var = str_replace("", "i", $var);
		
		$var = str_replace("ó", "o", $var);
		$var = str_replace("ò", "o", $var);
		$var = str_replace("ô", "o", $var);
		$var = str_replace("õ", "o", $var);
		$var = str_replace("º", "o", $var);
		
		$var = str_replace("ú", "u", $var);
		$var = str_replace("ù", "u", $var);
		$var = str_replace("û", "u", $var);
		//$var = str_replace("", "i", $var);
		
		$var = str_replace("ç", "c", $var);
		
		return $var;
	}
	
	/**
	 * Verifica se uma pasta com o nome informado já existe.
	 * Se não existe, cria a pasta.
	 */
	private function criaPastaPrincipal() {
		
		if ($this->verificaDiretorio()) { // já existe uma pasta com esse nome
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=19');
		} else {
			if (mkdir($this->caminhoPasta.$this->nomePasta, 0755)) {
				return true;
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=20');
			}
		}
		return false;
	}
	
	/**
	 * Procura por uma pasta com o mesmo nome.
	 */
	private function verificaDiretorio() {
		if (is_dir($this->caminhoPasta.$this->nomePasta)) {
			return true;
		}
		return false;
	}
	
	public function apagarAction() {
		if (DataValidator::isNumeric($_GET['id'])) {
			
			//apagando o contato
			$objeto = new ClienteModel();
			
			$objeto->loadById($_GET['id']);
			
			//if ($objeto->delete() && $this->excluiDiretorio($objeto->getNomePasta())) {
			//if ($objeto->desativa()) {
			if ($objeto->delete()) {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=3'); // sucesso
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=4');
			}
		}
	}
	
	/**
	 * Método para atualizar o cadastro do cliente logado no sistema.
	 * É chamado ao clicar no botão 'Salvar' do formulário.
	 */
	public function updateAction() {
		$objeto = new ClienteModel();
		
		//verificando se o id foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado � valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			}
		}
		
		if (count($_POST) > 0) {
			
			$objeto->setContrato(DataFilter::cleanString($_POST['contrato']));
			$objeto->setNomeFantasia(DataFilter::cleanString($_POST['nFantasia']));
			$objeto->setRazaoSocial(DataFilter::cleanString($_POST['rSocial']));
			$objeto->setCnpj(DataFilter::cleanString($_POST['cnpj']));
			$objeto->setIdCidade(DataFilter::cleanString($_POST['idCidade']));
			$objeto->setClienteStatus(DataFilter::cleanString($_POST['status']));
			
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

	/**
	 * Exclui o diretório do cliente se o cliente for excluído do BD.
	 */
	 // private function excluiDiretorio($nomePasta) {
	 	// //if (is_dir($this->caminhoPasta.$nomePasta)) {
		  // if (is_dir($nomePasta)) {
		 	// //if (rmdir($this->caminhoPasta.$nomePasta)) {
		 	// if (rmdir($nomePasta)) {
				// return true;
			// } else {
				// //return false;
				// Application::redirect('?controle='.$this->controle.'&acao=listar&msg=23');
			// }
		// } else {
			// Application::redirect('?controle='.$this->controle.'&acao=listar&msg=24');
		// }
	 // }
}
?>