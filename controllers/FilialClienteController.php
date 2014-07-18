<?php

require_once 'models/FilialClienteModel.php';
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - FilialClienteController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
class FilialClienteController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "FilialCliente";
	
	private $pastaCliente = "";
	private $nomeFilial = "";
	private $pastaFilial = "";
	private $pastaRelatorios = "";
	private $pastaFotos = "";
	
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
		$objeto = new FilialClienteModel();
		
		// Listando os perfis cadastrados
		$vetor = $objeto->_list($_REQUEST['id'], $_REQUEST['cliente'], $_REQUEST['filial']);
		
		// definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/'.$this->controle.'View.phtml');
		
		// Passando os dados do perfil para a View
		$o_view->setParams(array('vetor' => $vetor));
		
		// Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	public function manterAction() {
		$objeto = new FilialClienteModel();
		
		//print $_REQUEST['id']; break;
		
		//verificando se o id foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado é valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados do contato
				//print $_REQUEST['id']; break;
				$objeto->loadById($_REQUEST['id']);
			}
		} 
		
		//echo count($_POST); break;
		if (count($_POST) > 0) {
			// Garante que se está inserindo um novo registro.
			if (!isset($_REQUEST['id']) || $_REQUEST['id'] == "" || is_null($_REQUEST['id'])) { // se não tem id
				//print "cliente: ".$_POST['idcliente']." e filial: ".$_POST['idfilial']; break;
			
				if (isset($_POST['idcliente']) && isset($_POST['idfilial'])) {
					$this->pastaCliente = $this->buscaPastaCliente($_POST['idcliente']);
					$this->nomeFilial = $this->buscaNomeFilial($_POST['idfilial']);
					$this->criaPastaFilial();
				} else {
					Application::redirect('?controle='.$this->controle.'&acao=listar&msg=18');
				}
			}
			
			$objeto->setPkdoCliente(DataFilter::cleanString($_POST['idcliente']));
			$objeto->setPkdaFilial(DataFilter::cleanString($_POST['idfilial']));
			$objeto->setVinculoStatus(DataFilter::cleanString($_POST['status']));
			$objeto->setVinculoStatusAlterado(date('Y-m-d'));
			$objeto->setPastaFilial($this->pastaFilial);
			
			//salvando dados e redirecionando para a lista de contatos
			if ($objeto->save() > 0) {
				//print $objeto->pk_filialCliente; break;
				
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
	
	/**
	 * Busca a pasta do cliente de acordo com 
	 * o cliente selecionado na view.
	 */
	private function buscaPastaCliente($idCliente) {
		require_once 'models/ClienteModel.php';
		
		$cliente = new ClienteModel();
		$vCliente = $cliente->_list($idCliente);
		
		foreach ($vCliente as $item) {
			$pasta = $item->getNomePasta();
		}
		return $pasta;
	}
	
	/**
	 * Busca o nome da filial informada na view.
	 */
	private function buscaNomeFilial($idFilial) {
		require_once 'models/FilialModel.php';
		
		$filial = new FilialModel();
		$vFilial = $filial->_list($idFilial);
		
		foreach ($vFilial as $item) {
			$nomeFilial = $item->getFilial();
		}
		return $nomeFilial;
	}
	
	/**
	 * Cria a pasta com o nome da filial dentro da pasta do cliente selecionado na view.
	 * Cria as pastas 'relatorios' e 'fotos' dentro da pasta da filial selecionada na view.
	 */
	private function criaPastaFilial() {
		$caminho = $this->pastaCliente."/";

		if (is_dir($caminho)) {
			$nome = $this->trataNomeFilial();
			
			//print $nome; break;
			
			if (mkdir($caminho.$nome)) {
				$this->pastaFilial = $caminho.$nome;
				
				$caminhoRelatorios = $caminho.$nome."/relatorios";
				$this->pastaRelatorios = $caminhoRelatorios;
				
				$caminhoFotos = $caminho.$nome."/fotos";
				$this->pastaFotos = $caminhoFotos;
				
				if ($this->criaPastaRelatorios($caminhoRelatorios) && $this->criaPastaFotos($caminhoFotos)) {
					return true;
				}
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=26');
			}
		} else {
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=25');
		}
	}
	
	/**
	 * Cria a pasta 'relatorios' dentro da pasta da filial
	 */
	private function criaPastaRelatorios($caminhoRelatorios) {
		if (mkdir($caminhoRelatorios)) {
			return true;
		} else {
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=27');
		}
	}
	
	/**
	 * Cria a pasta 'fotos' dentro da pasta da filial
	 */
	private function criaPastaFotos($caminhoFotos) {
		if (mkdir($caminhoFotos)) {
			return true;
		} else {
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=28');
		}
	}
	
	public function apagarAction() {
		if (DataValidator::isNumeric($_GET['id'])) {
			
			//apagando o contato
			$objeto = new FilialClienteModel();
			
			$objeto->loadById($_GET['id']);
			
			$this->pastaCliente = $this->buscaPastaCliente($objeto->getPkdoCliente());
			$this->nomeFilial = $this->buscaNomeFilial($objeto->getPkdaFilial());
			
			//print $objeto->getPkdaFilial(); break;
			
			//if ($objeto->delete() && $this->excluirSubdiretorios()) {
			//if ($objeto->desativa()) {
			if ($objeto->delete()) {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=3'); // sucesso
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=4');
			}
		}
	}
	
	/**
	 * Exclui estrutura de pastas correspondente, de dentro da pasta do cliente,
	 * ao desvincular o cliente de uma filial.
	 */
	// private function excluirSubdiretorios() {
		// $this->pastaFilial = $this->trataNomeFilial();
// 		
		// // se existe a pasta do cliente
		// if (is_dir($this->pastaCliente)) {
// 
			// // se existe a pasta da filial
			// if (is_dir($this->pastaCliente."/".$this->pastaFilial)) {
// 				
				// // verifica se a pasta da filial está vazia
				// if (count(scandir($this->pastaCliente."/".$this->pastaFilial)) > 2) { // maior que 2 porque considera-se que existem os diretórios '.' e '..' do windows.
					// //exclui os subdiretorios e arquivos.
				// } else {
					// // exclui a pasta da filial se estiver vazia
					// if (rmdir($this->pastaCliente."/".$this->pastaFilial)) {
						// print $this->pastaCliente."/".$this->pastaFilial; break;
// 						
						// return true;
					// } else {
						// print $this->pastaCliente."/".$this->pastaFilial; break;
						// Application::redirect('?controle='.$this->controle.'&acao=listar&msg=30');
					// }
				// }
			// } else {
				// Application::redirect('?controle='.$this->controle.'&acao=listar&msg=29');
			// }
		// } else {
			// Application::redirect('?controle='.$this->controle.'&acao=listar&msg=24');
		// }
	// }
	
	/**
	 * Pega apenas o último nome da filial e trata
	 * retirando a acentuação e reescrevendo em minúsculo.
	 */
	private function trataNomeFilial() {
		$nome = explode(" ", $this->nomeFilial);
		
		$max = sizeof($nome);
		
		$var = strtolower($nome[$max-1]);
		
		$var = str_replace("á", "a", $var);
		$var = str_replace("à", "a", $var);
		$var = str_replace("â", "a", $var);
		$var = str_replace("ã", "a", $var);
		$var = str_replace("ª", "a", $var);
		
		$var = str_replace("é", "e", $var);
		$var = str_replace("è", "e", $var);
		$var = str_replace("ê", "e", $var);
		//$var = str_replace("~e", "e", $var);
		
		$var = str_replace("í", "i", $var);
		$var = str_replace("ì", "i", $var);
		$var = str_replace("î", "i", $var);
		//$var = str_replace("~i", "i", $var);
		
		$var = str_replace("ó", "o", $var);
		$var = str_replace("ò", "o", $var);
		$var = str_replace("ô", "o", $var);
		$var = str_replace("õ", "o", $var);
		$var = str_replace("º", "o", $var);
		
		$var = str_replace("ú", "u", $var);
		$var = str_replace("ù", "u", $var);
		$var = str_replace("û", "u", $var);
		//$var = str_replace("~u", "u", $var);
		
		$var = str_replace("ç", "c", $var);
		
		return $var;
	}
}
?>