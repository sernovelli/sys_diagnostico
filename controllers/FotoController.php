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
	
	// fotos
	private $caminho = "";
	private $nomeArquivo = "";
	private $extensao = "";
	private $nomeMiniatura = "";
	
	// cliente
	private $idFilialCliente;
	
	// log de envio de fotos
	private $vObjetoLog = array();

	public function indexAction() {
		// redireciona para a página de login caso o acesso seja
		// feito de forma direta, sem informar a ação na URL.
		header('Location: ?controle=Sair&acao=encerraSessao');
	}

	/**
	 * Aprensenta��o da lista de fotos
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
			
/************* UPLOAD DAS FOTOS ENVIADAS **********************************************************/
			$arquivosFotos = $this -> diverse_array($_FILES["foto"]);
			$i = 0;
			//print count($arquivosFotos)." fotos."; break;
			/**
			 * Configura o upload do arquivo e busca os dados do mesmo para gravar em BD.
			 */
			// inclui a classe para realizar o upload.
			require_once 'includes/UploadHelper.php';
			
			// inclui a classe para gerar o log de envio.
			require_once 'includes/GeraLogEnvioFotos.php';
			
			// Faz um loop nos arquivos enviados
			foreach ($arquivosFotos as $key => $error) {
				// objeto do arquivo para upload
				$upload = new UploadHelper();
				
				// objeto do log
				$logEnvio = new GeraLogEnvioFotos();
				
				/**
				 * Se está enviando uma foto (preencheu o campo 'foto'), faz o upload do
				 * arquivo e preenche o objeto com seus respectivos dados independente
				 * se está SALVANDO ou EDITANDO o registro.
				 */
				if ($_FILES['foto']['name'][$i] != "") {
					 
					// Pega a foto do $i e setta para upload.
					$file = (count($arquivosFotos) > 0) ? $arquivosFotos[$i] : FALSE;
					$upload -> set_file($file);
					
					if ($file != FALSE) {
						$logEnvio -> setLogEnviaArquivo(TRUE);
					} else {
						$logEnvio -> setLogEnviaArquivo(FALSE);
					}
	
					/**
					 * Define o diretório para onde o arquivo deverá ser enviado
					 * de acordo com o cliente selecionado na view.
					 */
					$this -> caminho = $this -> buscaPastaClienteFilial($_POST['idcliente'], $_POST['idFilial']);
					$caminho = $this -> caminho . "/fotos/";
	
					$this -> criaPastaDoDia($caminho);
					$upload -> set_uploads_folder($this -> caminho);
					
					//print $this -> caminho; break;
	
					// Define as extensões válidas
					$allowed = array('jpg', 'jpeg', 'png');
					$upload -> set_allowed_exts($allowed);
	
					// Renomeia o arquivo tratando-o.
					$this -> nomeArquivo = $arquivosFotos[$i]['name'];
					$logEnvio -> setLogNomeArquivoEnviado($this -> nomeArquivo);
					
					// $upload->set_file_name($this->trataNomeArquivo().".".$this->extensao);
					$upload -> set_file_name($this -> trataNomeArquivo());
					$logEnvio -> setLogRenomeiaArquivo(TRUE);
	
					// Deseja sobrescrever o arquivo como mesmo nome, caso exista? (True é o padrão)
					$upload -> set_overwrite(TRUE);
	
					// Qual o limite de tamanho dos arquivos? (2MB é o pdrão)
					$upload -> set_max_size(5);
					// em MegaBytes(MB)
	
					// setta os dados do arquivo para gravar no banco de dados
					$objeto -> setFoto($this -> nomeArquivo);
					$objeto -> setCaminhoFoto($this -> caminho);
					$objeto -> setTamanho($arquivosFotos[$i]['size']);
					$objeto -> setTipo($this -> extensao);
					$objeto -> setIdFilialCliente($this -> idFilialCliente);
					
					// executado se está salvando ou enviou imagem durante a edição.
					if ($upload -> upload_file()) {
		
						//print $this->caminho."/".$this->nomeArquivo.".".$this->extensao; echo "<br />";//break;
		
						// busca informações da imagem upada.
						$imnfo = getimagesize($this -> caminho . "/" . $this -> nomeArquivo . "." . $this -> extensao);
						$img_w = $imnfo[0];
						$img_h = $imnfo[1];
		
						/** Trata a proporção das medidas da imagem. 
						 * Se a imagem tiver largura maior que a altura, então fixa a largura e a altura será proporcional.
						 * Se a imagem tiver a altura maior que a largura, então fixa a altura e a largura será proporcional.
						 * Se a imagem tiver exatamente 480 x 640px ou 640 x 480px, mantém essas medidas.
						 */
						if ($img_w > $img_h && $img_w > 480) {
							$img_w = 480;
							$img_h = null;
						} else if ($img_h > $img_w && $img_h > 640) {
							$img_w = null;
							$img_h = 640;
						} else {
							$img_w = $imnfo[0];
							$img_h = $imnfo[1];
						}
						
						//print "largura: ".$img_w." x altura: ".$img_h; break;
		
						// Redimensiona a foto original - 480x640px
						// $imagem, $largura,$altura, $pasta, $criarSufixo, $qualidadeJpg, $qualidadePng, $qualidadeGif, $desejaCortar, $forcaDownload, $exibirBrowser
						$this -> trataFoto($this -> caminho . "/" . $this -> nomeArquivo . "." . $this -> extensao, $img_w, $img_h, $this -> caminho . "/", false, 80, 9, 100, false, false, false);
						$logEnvio -> setLogRedimensionaArquivo(TRUE);
		
						// Duplica a foto original - com sufixo '_mini' - para criar a miniatura
						if ($this -> duplicaFoto()) {
							// $imagem, $largura, $altura, $pasta, $criarSufixo, $qualidadeJpg, $qualidadePng, $qualidadeGif, $desejaCortar, $forcaDownload, $exibirBrowser
							$this -> trataFoto($this -> nomeMiniatura, 160, 120, $this -> caminho . "/", false, 100, 9, 100, true, false, false);
		
							$objeto -> setMiniatura($this -> nomeMiniatura);
							$logEnvio -> setLogCriaMiniatura(TRUE);
						} else {
							$logEnvio -> setLogCriaMiniatura(FALSE);
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
					// Ativa a notificação de postagens por email ao cliente
					$objeto -> ativaNotificarFotos($_POST['idcliente']);
					
					// Adicionou novo registro
					$logEnvio -> setLogSalvaEmBD(TRUE);
					
					// adiciona o log no vetor de log de envio.
					array_push($this->vObjetoLog,$logEnvio);

				} else if ($objeto -> save() == "") {
					// Ativa a notificação de postagens por email ao cliente
					$objeto -> ativaNotificarFotos($_POST['idcliente']);
					
					// Atualizou o registro
					$logEnvio -> setLogSalvaEmBD(TRUE);
					
				} else {
					$logEnvio -> setLogSalvaEmBD(FALSE);
				}
				
				++$i;
				$logEnvio -> setLogContaArquivos($i);
				$logEnvio -> validaLogs();
			} // fecha foreach que percorre as fotos enviadas.
			
			// echo "<pre>";
			// print_r($this->vObjetoLog);
			// echo "</pre>"; break;
// 			
			// direciona para a página de log de envio de fotos.
			$o_view = new View('views/FotoLogView.phtml');
			$o_view -> setParams(array('vetor' => $this->vObjetoLog));
			$o_view -> showContents();
		}

		// executa ao abrir o formulário para ADICIONAR ou EDITAR uma foto.
		$o_view = new View('views/Manter' . $this -> controle . '.phtml');
		$o_view -> setParams(array('objeto' => $objeto));
		$o_view -> showContents();
	}
	
	/**
	 * Método para tratar vetor de arquivos enviados.
	 * Transforma em um array de arrays, sendo cada "arrays" um arquivo diferente
	 * com suas respectivas informações.
	 */
	 private function diverse_array($vector) { 
	    $result = array(); 
	    foreach($vector as $key1 => $value1) {
	        foreach($value1 as $key2 => $value2) { 
	            $result[$key2][$key1] = $value2; 
		   }
	    }
	    return $result;
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
	private function trataFoto($imagem, $largura, $altura, $pasta, 
						  $criarSufixo, $qualidadeJpg, $qualidadePng, 
						  $qualidadeGif, $desejaCortar, $forcaDownload, 
						  $exibirBrowser) {

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
		$this -> nomeArquivo = $var;

		return $var;
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
			
			if ($_GET['pag'] == "") {
				$pag = "&pag=1";
			} else {
				$pag = "&pag=".$_GET['pag'];
			}

			if ($objeto -> delete() && $this -> excluiArquivo()) {
				// sucesso
				Application::redirect('?controle=' . $this -> controle . '&acao=listar'.$pag.'&msg=3');
			} else {
				Application::redirect('?controle=' . $this -> controle . '&acao=listar'.$pag.'&msg=4');
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
		
		if ($_GET['pag'] == "") {
			$pag = "&pag=1";
		} else {
			$pag = "&pag=".$_GET['pag'];
		}

		if ($objeto -> publicar()) {
			Application::redirect('?controle=' . $this -> controle . '&acao=listar'.$pag.'&msg=31');
		} else {
			Application::redirect('?controle=' . $this -> controle . '&acao=listar'.$pag.'&msg=32');
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