<?php
// inicia a sessao
ob_start();	
session_start();

require_once 'models/ArquivoModel.php';

/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - ArquivoController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
class ArquivoController {
	
	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Arquivo";
	
	private $caminho = "";
	
	private $nomeArquivo = "";
	private $extensao = "";
	private $idFilialCliente;
	
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
		
		$objeto = new ArquivoModel();
		
		//Listando os perfis cadastrados
		if ($_REQUEST['filtro'] == 1) {
			
			$vetor = $objeto->_filtros($_REQUEST['status'], $_REQUEST['filial'], $_REQUEST['cliente']);
		} else {
			$vetor = $objeto->_list();
		}
		
		//definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/'.$this->controle.'View.phtml');
		
		//Passando os dados do perfil para a View
		$o_view->setParams(array('vetor' => $vetor));
		
		//Imprimindo codigo HTML
		$o_view->showContents();
	}
	
	/* 
	 * Visualiza os arquivos pdf no navegador.
	 */
	public function verAction() {
		$objeto = new ArquivoModel();
		
		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado � valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				// buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			}
		}
		
		// url de visualização em localhost
		//$url = "dinamicarh/geraarquivos/".$objeto->getCaminho()."/".$objeto->getNomeArquivo().".".$objeto->getTipo();
		
		// url de visualização em servidor remoto
		$url = "arquivos_testar/".$objeto->getCaminho()."/".$objeto->getNomeArquivo().".".$objeto->getTipo();
		
		Application::redirect('/'.$url);
	}
	
	 /**
	  * Redireciona para o download do arquivo pdf/ppt
	  */
	 public function baixarAction() {
	 	$objeto = new ArquivoModel();
		
		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado � valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				// buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			}
		}

		$arquivo = $objeto->getCaminho()."/".$objeto->getNomeArquivo().".".$objeto->getTipo();
		$tipo = $objeto->getTipo();
		
		// redireciona para o arquivo de download.
		//Application::redirect('?controle='.$this->controle.'&acao=listar&msg=1');
		require_once "includes/Download.php";
		$obj = new Download($arquivo, $tipo);
	 }
	 
	public function manterAction() {
		$objeto = new ArquivoModel();
		
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
			$objeto->setDescricao(DataFilter::cleanString($_POST['descricao']));
			
			$dataInclusao = date("Y-m-d");
			$objeto->setDtInclusao($dataInclusao);

            	$dtVence = $this->calculaDataVencimento($dataInclusao);
            	$objeto->setDtVence(DataFilter::cleanString($dtVence));

            	$dtExclusao = $this->calculaDataExclusao($dataInclusao);
			$objeto->setDtExclusao(DataFilter::cleanString($dtExclusao));
			
			$objeto->setNotificar(DataFilter::cleanString($_POST['notificar']));
			$objeto->setArquivoStatus(DataFilter::cleanString($_POST['status']));
			
			/**
			 * configura o upload do arquivo e busca os dados do mesmo para gravar em BD.
			 */
			require_once 'includes/UploadHelper.php';
   			$upload = new UploadHelper();
			
			/**
			 * Se está enviando uma foto (preencheu o campo 'foto'), faz o upload do
			 * arquivo e preeche o objeto com seus respectivos dados indiferente
			 * de estar SALVANDO ou EDITANDO o registro.
			 */
			
			//Depois de submeter um formulário
			$file = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
			
			if ($file['name'] != "") {
				$upload->set_file($file);
			
				/** 
				 * Define o diretório para onde o arquivo deverá ser enviado
				 * de acordo com o cliente selecionado na view.
				 */
				//print "cliente: ".$_POST['idcliente']." filial: ".$_POST['idFilial']; break;
				
				$this->caminho = $this->buscaPastaClienteFilial($_POST['idcliente'],$_POST['idFilial']);
				
				//print $this->caminho; break;
				
				$caminho = $this->caminho."/relatorios/";
				
				//print $this->caminho."/relatorios/"; break;
				
				$this->criaPastaDoDia($caminho);
				$upload->set_uploads_folder($this->caminho);
				
				//print $this->caminho; break;
				
				// Define as extensões válidas
				$allowed = array('pdf', 'ppt', 'pptx', 'pps', 'xls', 'xlsx');
				$upload->set_allowed_exts($allowed);
				
				// Renomeia o arquivo tratando-o.
				$this->nomeArquivo = $file['name'];
				$upload->set_file_name($this->trataNomeArquivo());
				
				// Deseja sobrescrever o arquivo como mesmo nome, caso exista? (True é o padrão)
				$upload->set_overwrite(true);
				
				// Qual o limite de tamanho dos arquivos? em MegaBytes (MB)
				$upload->set_max_size(100);

				// setta os dados do arquivo para gravar no banco de dados
				$objeto->setNomeArquivo($this->nomeArquivo);
				$objeto->setCaminho($this->caminho);
				$objeto->setTamanho($file['size']);
				$objeto->setTipo($this->extensao);
				$objeto->setIdFilialCliente($this->idFilialCliente);
			} else {
				/**
				 * Caso esteja editando o registro sem alterar a foto, então busca 
				 * os dados atuais do arquivo/foto no banco de dados.
				 */
				$objeto2 = new ArquivoModel();
				$objeto2->loadById($_REQUEST['id']);
				
				// setta os dados do arquivo para gravar no banco de dados
				$objeto->setNomeArquivo(DataFilter::cleanString($objeto2->getNomeArquivo()));
				$objeto->setCaminho(DataFilter::cleanString($objeto2->getCaminho()));
				$objeto->setTamanho(DataFilter::cleanString($objeto2->getTamanho()));
				$objeto->setTipo(DataFilter::cleanString($objeto2->getTipo()));
			}
			
			//print $this->caminho; break;
			
			// faz o upload
			if ($upload->upload_file()) {
		
			     //salvando dados e redirecionando para a lista de registros
				if ($objeto->save() > 0) {
					Application::redirect('?controle='.$this->controle.'&acao=listar&msg=1');
				} else 
				if ($objeto->save() == "") {
					
					// Ativa a notificação de postagens por email ao cliente
					$objeto -> ativaNotificarArquivos($_POST['idcliente']);
					
					// Atualizou o registro
					Application::redirect('?controle='.$this->controle.'&acao=listar&msg=15');
				} else {
					// Erro na operação
					Application::redirect('?controle='.$this->controle.'&acao=listar&msg=2');
				}
			}
			else {
			    Application::redirect('?controle='.$this->controle.'&acao=listar&msg=36');
			}
		}
		
		$o_view = new View('views/Manter'.$this->controle.'.phtml');
		$o_view->setParams(array('objeto' => $objeto));
		$o_view->showContents();
	}
	
	/**
	 * Cria a pasta no caminho montado cujo nome é a data atual, 
	 * somente se essa pasta já não existir.
	 */
	private function criaPastaDoDia($caminho) {
		$pastaDia = date("dmY");
		$novoCaminho = $caminho.$pastaDia;
		
		//print $novoCaminho; break;
		$this->caminho = $novoCaminho;
		
		if (!is_dir($novoCaminho)) {
		
			if (mkdir($novoCaminho)) {
				return $novoCaminho;
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=27');
			}
		}
	}
	
	/**
	 * Busca caminho/nome da pasta do cliente
	 */
	 private function buscaPastaClienteFilial($idCliente,$idFilial) {
	 	
		//print "idCliente: ".$idCliente." - idFilial: ".$idFilial; break;	
		
	 	require_once 'models/FilialClienteModel.php';
		$cliente = new FilialClienteModel();
		
		$vetor = $cliente->listaFilialCliente($idCliente,$idFilial);
		
		//print $vetor->getPastaFilial(); break;
		
		$this->idFilialCliente = $vetor->getPkFilialCliente();
		
		//print $vetor->getPastaFilial(); break;
		
		return $vetor->getPastaFilial();
	 }
	
	/**
	 * Pega apenas o último nome da filial e trata
	 * retirando a acentuação e reescrevendo em minúsculo.
	 */
	private function trataNomeArquivo() {
		
		$id = uniqid();
		
		$nomeAntigo = $this->caminho."/".$this->nomeArquivo;
		
		$novoNome = "relatorio".$id.date("dmYHis");
		
		$var = $novoNome;

		$this->extensao = strtolower(array_pop(explode(".",$this->nomeArquivo)));
		
		//print $this->nomeArquivo; break;
		
		$this->nomeArquivo = $var;
		
		return $var;
		
		// // Converte a iniciais de cada palavra do nome para maiúsculo.
		// $nome = ucwords($this->nomeArquivo);
// 		
		// // retira espaços em branco do inicio e fim do nome do arquivo.
		// $var = trim($nome);
// 		
		// // retira os espaços em branco entre as palavras do nome do arquivo.
		// $var = str_replace(" ", "", $var);
// 		
		// // separa nome do arquivo da extensão
		// $nome = explode(".", $var);
		// $var = $nome[0];
		// $this->extensao = $nome[1]; // é o tipo do arquivo.
// 		
		// retira todo tipo de acentuação.
		// $var = str_replace("á", "a", $var);
		// $var = str_replace("à", "a", $var);
		// $var = str_replace("â", "a", $var);
		// $var = str_replace("ã", "a", $var);
		// $var = str_replace("ª", "a", $var);
// 		
		// $var = str_replace("é", "e", $var);
		// $var = str_replace("è", "e", $var);
		// $var = str_replace("ê", "e", $var);
		// //$var = str_replace("~e", "e", $var);
// 		
		// $var = str_replace("í", "i", $var);
		// $var = str_replace("ì", "i", $var);
		// $var = str_replace("î", "i", $var);
		// //$var = str_replace("~i", "i", $var);
// 		
		// $var = str_replace("ó", "o", $var);
		// $var = str_replace("ò", "o", $var);
		// $var = str_replace("ô", "o", $var);
		// $var = str_replace("õ", "o", $var);
		// $var = str_replace("º", "o", $var);
// 		
		// $var = str_replace("ú", "u", $var);
		// $var = str_replace("ù", "u", $var);
		// $var = str_replace("û", "u", $var);
		// $var = str_replace("~u", "u", $var);
		
		// $var = str_replace("ç", "c", $var);;
// 		
		// $this->nomeArquivo = $var;
// 		
		// return $var;
	}
	
	public function apagarAction() {
		if (DataValidator::isNumeric($_GET['id'])) {
			
			//apagando
			$objeto = new ArquivoModel();
			
			$objeto->loadById($_GET['id']);
			
			$this->caminho = $objeto->getCaminho();
			$this->nomeArquivo = $objeto->getNomeArquivo();
			$this->extensao = $objeto->getTipo();
			
			if ($objeto->delete() && $this->excluiArquivo()) {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=3'); // sucesso
			} else {
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=4');
			}
		}
	}
	
	/**
	 * Exclui o arquivo de relatorio do FTP.
	 */
	 public function excluiArquivo() {
		
		if (is_dir($this->caminho)) {
			$arquivo = $this->caminho."/".$this->nomeArquivo.".".$this->extensao;
			//print $arquivo; break;
			
			if (file_exists($arquivo)) {
				if (unlink($arquivo)) {
					return true;
				} else {
					// Não foi possível excluir este arquivo do FTP.
					Application::redirect('?controle='.$this->controle.'&acao=listar&msg=33');
				}
			} else {
				// este arquivo não existe no caminho especificado do FTP.
				Application::redirect('?controle='.$this->controle.'&acao=listar&msg=34');
			}
		} else {
			// o diretório desse arquivo não existe ou não foi encontrado.
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=35');
		}
	 }
	 
	 /**
	  * Ativa/desativa o aquivo, mas não exclui
	  * do banco de dados, nem do FTP.
	  */
	 public function ativarAction() {

		$objeto = new ArquivoModel();
		$objeto->loadById($_GET['id']);
		
		if ($objeto->publicar()) {
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=31'); // sucesso
		} else {
			Application::redirect('?controle='.$this->controle.'&acao=listar&msg=32');
		}
		
	 }
	 
	 /**
	 * Calcula da data de vencimento, ou seja, que a foto vai sair
	 * da visualização do cliente (não será excluida).
	 */
	 private function calculaDataVencimento($dataInclusao) {
	 	$dataVencimento = date('Y-m-d', strtotime("+90 days",strtotime($dataInclusao)));
		return $dataVencimento;
	 }
	 
	 /**
	 * Calcula da data de exclusão, ou seja, data em que a foto será excluida.
	 */
	 private function calculaDataExclusao($dataInclusao) {
	 	$dataExclusao = date('Y-m-d', strtotime("+120 days",strtotime($dataInclusao)));
		return $dataExclusao;
	 }
}
?>