<?php

require_once 'models/FotoModel.php';
// Chama o arquivo com a classe WideImage
//require_once 'includes/wideimageHelper/WideImage.php';
require_once "includes/tratamentoFotos/image.php";
require_once "includes/tratamentoFotos/browser.php";
require_once "includes/tratamentoFotos/thumb.php";
/**
 *
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 *
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - FotoController.php
 *
 * @author Sergio Novelli
 * @version 1.0
 *
 */

class FotoController {

	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações CRUD.
	private $controle = "Foto";

	private $caminho = "";
	//private $caminhoTmp = "d:\\wamp\\temp\\";
	//private $caminhoTmp = "d:\\wamp\\temp\\";

	private $nomeArquivo = "";
	private $extensao = "";
	private $nomeMiniatura = "";
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
		$objeto = new FotoModel();
		
		$qtRegistrosExibir = 6;
		// parâmetros para a paginação
		if (isset($_REQUEST['pag'])) {
			$objeto->setPg($_REQUEST['pag']);
			$objeto->setInicio(($qtRegistrosExibir * $_REQUEST['pag']) - $qtRegistrosExibir);
			$objeto->setQtRegistrosExibir($qtRegistrosExibir);
		} else {
			$objeto->setPg(1);
			$objeto->setInicio(($qtRegistrosExibir * 1) - $qtRegistrosExibir);
			$objeto->setQtRegistrosExibir($qtRegistrosExibir);
		}
		
		//Listando os perfis cadastrados
		if ($_REQUEST['filtro'] == 1) {
			//print "status: ". $_REQUEST['status']; break;
			$vetor = $objeto->_filtros($_REQUEST['status'], $_REQUEST['filial'], $_REQUEST['cliente']);
			
			//echo "<pre>"; print_r($vetor); echo "</pre>"; break;
		} else {
			$vetor = $objeto -> _list();
		}
		
		//definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/' . $this -> controle . 'View.phtml');

		//Passando os dados do perfil para a View
		$o_view -> setParams(array('vetor' => $vetor));

		//Imprimindo codigo HTML
		$o_view -> showContents();
	}

	/**
	 * Grava os dados da foto no BD e faz o upload da foto para o FTP.
	 */
	public function manterAction() {
		$objeto = new FotoModel();

		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado � valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				//buscando dados
				$objeto -> loadById($_REQUEST['id']);
			}
		}

		//echo count($_POST); break;
		if (count($_POST) > 0) {
			//echo "tem post"; break;
			$objeto -> setLoja(DataFilter::cleanString($_POST['loja']));
			$objeto -> setCoordenador(DataFilter::cleanString($_POST['coordenador']));
			$objeto -> setPromotor(DataFilter::cleanString($_POST['promotor']));

			$objeto -> setMesRefere(DataFilter::cleanString($_POST['mesReferencia']));

			$dataInclusao = date("Y-m-d");
			$objeto -> setDtInclusao($dataInclusao);

			$dtVence = $this -> calculaDataVencimento($dataInclusao);
			$objeto -> setDtVencimento(DataFilter::cleanString($dtVence));

			$dtExclusao = $this -> calculaDataExclusao($dataInclusao);
			$objeto -> setDtExclusao(DataFilter::cleanString($dtExclusao));

			$objeto -> setNotificar("1");
			
			// será alterado automaticamente ao fazer o envio dos emails automáticos.
			$objeto -> setFotoStatus(DataFilter::cleanString($_POST['status']));

			/**
			 * Configura o upload do arquivo e busca os dados do mesmo para gravar em BD.
			 */
			require_once 'includes/UploadHelper.php';
			$upload = new UploadHelper();

			/**
			 * Se está enviando uma foto (preencheu o campo 'foto'), faz o upload do
			 * arquivo e preeche o objeto com seus respectivos dados indiferente
			 * de estar SALVANDO ou EDITANDO o registro.
			 */

			//Depois de submeter um formulário
			$file = isset($_FILES['foto']) ? $_FILES['foto'] : FALSE;
			
			echo "<pre>";
			print $file; 
			echo "</pre>"; break;
			
			
			
			if ($file['name'] != "") { 

				$upload -> set_file($file);

				/**
				 * Define o diretório para onde o arquivo deverá ser enviado
				 * de acordo com o cliente selecionado na view.
				 */
				$this -> caminho = $this -> buscaPastaClienteFilial($_POST['idcliente'], $_POST['idFilial']);
				$caminho = $this -> caminho . "/fotos/";

				//print $this->caminho."/fotos/"; break;

				$this -> criaPastaDoDia($caminho);
				$upload -> set_uploads_folder($this -> caminho);
				
				print $this -> caminho; break;

				// Define as extensões válidas
				$allowed = array('jpg', 'jpeg', 'png');
				$upload -> set_allowed_exts($allowed);

				// Renomeia o arquivo tratando-o.
				$this -> nomeArquivo = $file['name'];
				
				//$upload->set_file_name($this->trataNomeArquivo().".".$this->extensao);
				$upload -> set_file_name($this -> trataNomeArquivo());

				// Deseja sobrescrever o arquivo como mesmo nome, caso exista? (True é o padrão)
				$upload -> set_overwrite(true);

				// Qual o limite de tamanho dos arquivos? (2MB é o pdrão)
				$upload -> set_max_size(5);
				// em MegaBytes(MB)

				// setta os dados do arquivo para gravar no banco de dados
				$objeto -> setFoto($this -> nomeArquivo);
				$objeto -> setCaminhoFoto($this -> caminho);
				$objeto -> setTamanho($file['size']);
				$objeto -> setTipo($this -> extensao);
				$objeto -> setIdFilialCliente($this -> idFilialCliente);
				
				// executado se está salvando ou enviou imagem durante a edição.
				if ($upload -> upload_file()) {
	
					//print $this->caminho."/".$this->nomeArquivo.".".$this->extensao; echo "<br />";//break;
	
					// busca informações da imagem upada.
					$imnfo = getimagesize($this -> caminho . "/" . $this -> nomeArquivo . "." . $this -> extensao);
					$img_w = $imnfo[0];
					$img_h = $imnfo[1];
	
					/** Se a imagem tiver largura maior que a altura,
					 * então fixa a largura e a altura será proporcional.
					 * Se a imagem tiver a altura maior que a largura,
					 * então fixa a altura e a largura será proporcional.
					 */
					if ($img_w > $img_h && $img_w > 480) {
						$img_w = 480;
						$img_h = null;
					} else if ($img_h > $img_w && $img_h > 640) {
						$img_w = null;
						$img_h = 640;
					} else {
						$img_w = null;
						$img_h = null;
					}
	
					// Redimensiona a foto original - 480x640px
					// $imagem, $largura,$altura, $pasta, $criarSufixo, $qualidadeJpg, $qualidadePng, $qualidadeGif, $desejaCortar, $forcaDownload, $exibirBrowser
					$this -> trataFoto($this -> caminho . "/" . $this -> nomeArquivo . "." . $this -> extensao, $img_w, $img_h, $this -> caminho . "/", false, 80, 9, 100, false, false, false);
	
					// Duplica a foto original - com sufixo '_mini' - para criar a miniatura
					if ($this -> duplicaFoto()) {
						// $imagem, $largura, $altura, $pasta, $criarSufixo, $qualidadeJpg, $qualidadePng, $qualidadeGif, $desejaCortar, $forcaDownload, $exibirBrowser
						$this -> trataFoto($this -> nomeMiniatura, 160, 120, $this -> caminho . "/", false, 100, 9, 100, true, false, false);
	
						$objeto -> setMiniatura($this -> nomeMiniatura);
					}
	
				} else {
					Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=36');
				}

			} else {
				/**
				 * Caso esteja editando o registro SEM ALTERAR A FOTO, então busca
				 * os dados atuais do arquivo/foto no banco de dados para regravar.
				 */
				$objeto2 = new FotoModel();
				$objeto2 -> loadById($_REQUEST['id']);

				// setta os dados do arquivo para gravar no banco de dados
				$objeto -> setFoto(DataFilter::cleanString($objeto2 -> getFoto()));
				$objeto -> setCaminhoFoto(DataFilter::cleanString($objeto2 -> getCaminhoFoto()));
				$objeto -> setTamanho(DataFilter::cleanString($objeto2 -> getTamanho()));
				$objeto -> setTipo(DataFilter::cleanString($objeto2 -> getTipo()));
				$objeto -> setMiniatura(DataFilter::cleanString($objeto2 -> getMiniatura()));
			}

			//salvando dados e redirecionando para a lista de contatos
			if ($objeto -> save() > 0) {
				Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=1');
			} else if ($objeto -> save() == "") {
				// Atualizou o registro
				Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=15');
			} else {
				// Erro na operação
				Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=2');
			}
		}

		$o_view = new View('views/Manter' . $this -> controle . '.phtml');
		$o_view -> setParams(array('objeto' => $objeto));
		$o_view -> showContents();
	}

	/**
	 * Duplica a imagem para criar a miniatura
	 */
	private function duplicaFoto() {
		$origem = $this -> caminho . "/" . $this -> nomeArquivo . "." . $this -> extensao;

		$destino = $this -> caminho . "/" . $this -> nomeArquivo . "_mini." . $this -> extensao;

		$this -> nomeMiniatura = $destino;
		// caminho completo da miniatura.

		if (copy($origem, $destino)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Redimensiona a imagem original
	 */
	private function trataFoto($imagem, $largura, $altura, $pasta, $criarSufixo, $qualidadeJpg, $qualidadePng, $qualidadeGif, $desejaCortar, $forcaDownload, $exibirBrowser) {

		//print $imagem; break;

		$thumb = new thumb($imagem);
		//link ou resource da imagem original
		$thumb -> setDimensions(array($largura, $altura));
		//largura e altura da thumb, aceita arrays multidimensionais
		$thumb -> setFolder($pasta);
		//caso queira que a thumb seja salva numa pasta
		$thumb -> sufix = $criarSufixo;
		//caso queira setar um sufixo -> imagem-750x320
		$thumb -> setJpegQuality($qualidadeJpg);
		//qualidade JPG (0-100)
		$thumb -> setPngQuality($qualidadePng);
		//qualidade do PNG (0-9)
		$thumb -> setGifQuality($qualidadeGif);
		//qualidade do GIF (0-100)
		$thumb -> crop = $desejaCortar;
		//se a imagem deverá ser cropada ou não
		$thumb -> forceDownload($forcaDownload);
		//true para setar a thumb para download
		$thumb -> showBrowser($exibirBrowser);
		//true para setar a thumb para mostrar no navegador

		$thumb -> process();
	}

	/**
	 * Cria a pasta no caminho montado cujo nome é a data atual,
	 * somente se essa pasta já não existir.
	 */
	private function criaPastaDoDia($caminho) {
		$pastaDia = date("dmY");
		$novoCaminho = $caminho . $pastaDia;

		//print $novoCaminho; break;
		$this -> caminho = $novoCaminho;

		if (!is_dir($novoCaminho)) {

			if (mkdir($novoCaminho)) {
				return $novoCaminho;
			} else {
				Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=27');
			}
		}
	}

	/**
	 * Busca caminho/nome da pasta do cliente
	 * Também busca o id da FilialCliente relacionada
	 * com a foto/arquivo.
	 */
	private function buscaPastaClienteFilial($idCliente, $idFilial) {
		require_once 'models/FilialClienteModel.php';
		$cliente = new FilialClienteModel();

		//print "cliente: ".$idCliente." filial: ".$idFilial; break;

		$vetor = $cliente -> listaFilialCliente($idCliente, $idFilial);

		$this -> idFilialCliente = $vetor -> getPkFilialCliente();

		return $vetor -> getPastaFilial();
	}

	/**
	 * Pega o nome do arquivo e trata
	 * retirando a acentuação e reescrevendo em minúsculo.
	 */
	private function trataNomeArquivo() {

		$id = uniqid();

		$nomeAntigo = $this -> caminho . "/" . $this -> nomeArquivo;

		$novoNome = "foto" . $id . date("dmYHis");

		$var = $novoNome;

		$this -> extensao = strtolower(array_pop(explode(".", $this -> nomeArquivo)));

		//print $this->nomeArquivo; break;

		$this -> nomeArquivo = $var;

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
		// // retira todo tipo de acentuação.
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
		// //$var = str_replace("~u", "u", $var);
		//
		// $var = str_replace("ç", "c", $var);
		//
		// $this->nomeArquivo = $var;
		//
		// return $var;
	}

	public function apagarAction() {
		if (DataValidator::isNumeric($_GET['id'])) {

			//apagando o contato
			$objeto = new FotoModel();

			$objeto -> loadById($_GET['id']);

			$this -> caminho = $objeto -> getCaminhoFoto();
			$this -> nomeArquivo = $objeto -> getFoto();
			$this -> extensao = $objeto -> getTipo();
			$this -> nomeMiniatura = $objeto -> getMiniatura();

			if ($objeto -> delete() && $this -> excluiArquivo()) {
				Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=3');
				// sucesso
			} else {
				Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=4');
			}
		}
	}

	/**
	 * Exclui o arquivo do FTP.
	 */
	public function excluiArquivo() {

		if (is_dir($this -> caminho)) {
			$arquivo = $this -> caminho . "/" . $this -> nomeArquivo . "." . $this -> extensao;
			$miniatura = $this -> nomeMiniatura;

			// exclui a foto maior.
			if (file_exists($arquivo) && file_exists($miniatura)) {

				if (unlink($arquivo) && unlink($miniatura)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Ativa/desativa o aquivo, mas não exclui
	 * do banco de dados, nem do FTP.
	 */
	public function ativarAction() {

		$objeto = new FotoModel();
		$objeto -> loadById($_GET['id']);

		if ($objeto -> publicar()) {
			Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=31');
		} else {
			Application::redirect('?controle=' . $this -> controle . '&acao=listar&msg=32');
		}
	}

	/**
	 * Calcula da data de vencimento, ou seja, que a foto vai sair
	 * da visualização do cliente (não será excluida).
	 */
	private function calculaDataVencimento($dataInclusao) {
		$dataVencimento = date('Y-m-d', strtotime("+90 days", strtotime($dataInclusao)));
		return $dataVencimento;
	}

	/**
	 * Calcula da data de exclusão, ou seja, data em que a foto será excluida.
	 */
	private function calculaDataExclusao($dataInclusao) {
		$dataExclusao = date('Y-m-d', strtotime("+120 days", strtotime($dataInclusao)));
		return $dataExclusao;
	}
	
	 /**
	  * Redireciona para o download do arquivo jpg
	  */
	 public function baixarAction() {
	 	$objeto = new FotoModel();
		
		//verificando se o id do contato foi passado
		if (isset($_REQUEST['id'])) {
			//verificando se o id passado � valido
			if (DataValidator::isNumeric($_REQUEST['id'])) {
				// buscando dados do contato
				$objeto->loadById($_REQUEST['id']);
			}
		}

		$arquivo = $objeto->getCaminhoFoto()."/".$objeto->getFoto().".".$objeto->getTipo();
		$tipo = $objeto->getTipo();
		
		// redireciona para o arquivo de download.
		//Application::redirect('?controle='.$this->controle.'&acao=listar&msg=1');
		require_once "includes/Download.php";
		$obj = new Download($arquivo, $tipo);
	 }
}
?>