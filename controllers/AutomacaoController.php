<?php
/**
 * Script para desativar fotos publicadas há 90 dias ou mais
 */
require_once 'models/DesativaFotosModel.php';
require_once 'models/DesativaArquivosModel.php';

require_once 'models/ExcluiFotosModel.php';
require_once 'models/ExcluiArquivosModel.php';

require_once 'models/NotificaFotosModel.php';
require_once 'models/NotificaArquivosModel.php';

class AutomacaoController {

	private $alteracoes = 0;
	private $erros = 0;
	private $resultado = "";

	private $caminho;
	private $caminhoFoto;
	private $caminhoMiniatura;

	private $caminhoArquivo;

	public function indexAction() {
		//header('Location: ?controle=Sair&acao=encerraSessao');
	}

	/*== FUNÇÕES COMUNS A TODAS AS AÇÕES =========================================================================================*/

	/**
	 * Redireciona para a view de testes
	 */
	private function redirToView($vetor) {
		//definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/AutomacaoView.php');

		//Passando os dados do perfil para a View
		$o_view -> setParams(array('vetor' => $vetor));

		//Imprimindo codigo HTML
		$o_view -> showContents();
	}

	/**
	 * Grava o log com os dados da foto, cliente e filial
	 */
	private function gravaLog($texto, $acao) {

		// DEFINE O NOME DO ARQUIVO E EXTENSÃO
		$nome_arquivo = $acao . "_" . date("d-m-Y") . ".txt";

		// CAMINHO ONDE SERÁ GRAVADO O ARQUIVO
		$arquivo = "logs/automacao/" . $nome_arquivo;

		// TENTA ABRIR O ARQUIVO TXT
		if (file_exists($nome_arquivo)) {
			if (!$abrir = fopen($arquivo, "w")) {

				$this -> resultado = "Erro ao abrir arquivo ($arquivo) pelo metodo [w]";

				$vetor = array('resultado' => $this -> resultado);

				$this -> redirToView($vetor);
			}
		} else {
			if (!$abrir = fopen($arquivo, "a")) {

				$this -> resultado = "Erro ao abrir arquivo ($arquivo) pelo metodo [a]";

				$vetor = array('resultado' => $this -> resultado);

				$this -> redirToView($vetor);
			}
		}

		// TENTA ESCREVER NO ARQUIVO TXT
		if (!fwrite($abrir, $texto)) {

			$this -> resultado = "Erro ao escrever no arquivo ($arquivo)";

			$vetor = array('resultado' => $this -> resultado);

			$this -> redirToView($vetor);

			//return "Erro ao escrever no arquivo ($arquivo)";
		}

		// FECHA O ARQUIVO INDEPENDENTE DE TER CONSEGUIDO ESCREVER OU NÃO O ARQUIVO.
		fclose($abrir);

		return true;
	}

	/*== EXCLUIR ARQUIVOS ===============================================================================*/

	public function excluirArquivosAction() {

		$objeto = new ExcluiArquivosModel();

		//Listando as fotos com a data de vencimento igual a data atual.
		$varquivos = $objeto -> listaArquivosExclusao();

		if (count($varquivos) > 0) {
			$cont = -1;
			// para cada foto encontrada, busca os dados de cliente e filial da foto.
			foreach ($varquivos as $item) {
				$cont++;
				// pega pk da foto atual
				$idArquivo = $item -> getPkArquivo();

				// busca dados do cliente vinculado a foto atual
				$vcliente = $objeto -> listaDadosCliente($idArquivo);

				// busca dados da filial vinculada a foto atual
				$vfilial = $objeto -> listaDadosFilial($idArquivo);

				// faz a gravação dos dados em log
				if ($this -> preparaLogExcluirArquivos($varquivos, $vcliente, $vfilial, $cont)) {

					// procura a foto e exclui do FTP antes de excluir do banco de dados.
					if ($this -> excluiArquivoFTP()) {

						// exclui a foto do banco de dados
						if ($objeto -> excluiArquivo($idArquivo)) {
							$this -> alteracoes++;
						} else {
							$this -> erros++;
						}
					} else {

						$this -> resultado = "Erro ao excluir arquivos do FTP.";

						$vetor = array('resultado' => $this -> resultado);

						$this -> redirToView($vetor);
					}
				}
			}

			$vetor = array('erros' => $this -> erros, 'alteracoes' => $this -> alteracoes);

			$this -> redirToView($vetor);
		} else {
			$this -> resultado = "Nenhum arquivo encontrado.";

			$vetor = array('resultado' => $this -> resultado);

			$this -> redirToView($vetor);
		}
	}

	/**
	 * Exclui a foto e a miniatura do FTP.
	 */
	public function excluiArquivoFTP() {

		if (is_dir($this -> caminho)) {

			//print $this->caminhoArquivo; break;

			$arquivo = $this -> caminhoArquivo;

			// exclui a foto maior.
			if (file_exists($arquivo)) {

				//print "entrou aqui... 2 "; break;

				if (unlink($arquivo)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Prepara o texto do log para ser gravado.
	 */
	private function preparaLogExcluirArquivos($varquivos, $vcliente, $vfilial, $cont) {

		// texto a ser gravado no log
		$texto = " \n *------------------------------------------------------------*";
		$texto .= " \n Data da ação: " . date("d/m/Y H:i:s");
		$texto .= " \n Ação: Arquivo excluído do FTP e do Banco de Dados.";
		$texto .= " \n Pk do arquivo: " . $varquivos[$cont] -> getPkArquivo();
		$texto .= " \n URL: " . $varquivos[$cont] -> getCaminho() . "/" . $varquivos[$cont] -> getNomeArquivo() . "." . $varquivos[$cont] -> getTipo();
		$texto .= " \n Data Exclusão: " . $varquivos[$cont] -> getDtExclusao();

		$texto .= " \n	Cliente: " . $vcliente -> getNomeFantasia();

		$texto .= " \n	Filial: " . $vfilial -> getFilial();

		$this -> caminho = $varquivos[$cont] -> getCaminho();
		$this -> caminhoArquivo = $varquivos[$cont] -> getCaminho() . "/" . $varquivos[$cont] -> getNomeArquivo() . "." . $varquivos[$cont] -> getTipo();

		$acao = "excluirArquivos";
		// grava log
		$this -> gravaLog($texto, $acao);

		return true;
	}

	/*== DESATIVAR ARQUIVOS =========================================================================================*/

	public function desativarArquivosAction() {

		$objeto = new DesativaArquivosModel();

		//Listando as fotos com a data de vencimento igual a data atual.
		$varquivos = $objeto -> listaArquivosVencimento();

		if (count($varquivos) > 0) {
			$cont = -1;
			// para cada foto encontrada, busca os dados de cliente e filial da foto.
			foreach ($varquivos as $item) {
				$cont++;
				// pega pk da foto atual
				$idArquivo = $item -> getPkArquivo();

				// busca dados do cliente vinculado a foto atual
				$vcliente = $objeto -> listaDadosCliente($idArquivo);

				// busca dados da filial vinculada a foto atual
				$vfilial = $objeto -> listaDadosFilial($idArquivo);

				// faz a gravação dos dados em log
				if ($this -> preparaLogArquivos($varquivos, $vcliente, $vfilial, $cont)) {

					// atualiza o registro da foto
					if ($objeto -> desativaArquivo($idArquivo)) {
						$this -> alteracoes++;
					} else {
						$this -> erros++;
					}
				}
			}

			$vetor = array('erros' => $this -> erros, 'alteracoes' => $this -> alteracoes);

			$this -> redirToView($vetor);
		} else {
			$this -> resultado = "Nenhum arquivo encontrado.";

			$vetor = array('resultado' => $this -> resultado);

			$this -> redirToView($vetor);
		}
	}

	/**
	 * Prepara o texto do log para ser gravado.
	 */
	private function preparaLogArquivos($varquivos, $vcliente, $vfilial, $cont) {

		// texto a ser gravado no log
		$texto = " \n *------------------------------------------------------------*";
		$texto .= " \n Data da ação: " . date("d/m/Y H:i:s");
		$texto .= " \n Ação: Arquivo Desativado/Despublicado";
		$texto .= " \n Pk do arquivo: " . $varquivos[$cont] -> getPkArquivo();
		$texto .= " \n URL: " . $varquivos[$cont] -> getCaminho() . "/" . $varquivos[$cont] -> getNomeArquivo() . "." . $varquivos[$cont] -> getTipo();
		$texto .= " \n Data Vencimento: " . $varquivos[$cont] -> getDtVence();

		$texto .= " \n	Cliente: " . $vcliente -> getNomeFantasia();

		$texto .= " \n	Filial: " . $vfilial -> getFilial();

		$acao = "desativarArquivos";

		// grava log
		$this -> gravaLog($texto, $acao);

		return true;
	}

	/*== EXCLUIR FOTOS ===============================================================================*/

	public function excluirFotosAction() {

		$objeto = new ExcluiFotosModel();

		//Listando as fotos com a data de vencimento igual a data atual.
		$vfotos = $objeto -> listaFotosExclusao();

		if (count($vfotos) > 0) {
			$cont = -1;
			// para cada foto encontrada, busca os dados de cliente e filial da foto.
			foreach ($vfotos as $item) {
				$cont++;
				// pega pk da foto atual
				$idFoto = $item -> getPkFoto();

				// busca dados do cliente vinculado a foto atual
				$vcliente = $objeto -> listaDadosCliente($idFoto);

				// busca dados da filial vinculada a foto atual
				$vfilial = $objeto -> listaDadosFilial($idFoto);

				// faz a gravação dos dados em log
				if ($this -> preparaLogExcluirFotos($vfotos, $vcliente, $vfilial, $cont)) {

					// procura a foto e exclui do FTP antes de excluir do banco de dados.
					if ($this -> excluiFotoFTP()) {

						// exclui a foto do banco de dados
						if ($objeto -> excluiFoto($idFoto)) {
							$this -> alteracoes++;
						} else {
							$this -> erros++;
						}
					} else {
						echo "erro ao excluir imagens do FTP.";
						break;
					}
				}
			}

			$vetor = array('erros' => $this -> erros, 'alteracoes' => $this -> alteracoes);

			$this -> redirToView($vetor);
		} else {
			$this -> resultado = "Nenhum arquivo encontrado.";

			$vetor = array('resultado' => $this -> resultado);

			$this -> redirToView($vetor);
		}
	}

	/**
	 * Exclui a foto e a miniatura do FTP.
	 */
	public function excluiFotoFTP() {

		if (is_dir($this -> caminho)) {

			//print $this->caminhoFoto; break;

			$arquivo = $this -> caminhoFoto;
			$miniatura = $this -> caminhoMiniatura;

			// exclui a foto maior.
			if (file_exists($arquivo) && file_exists($miniatura)) {

				//print "entrou aqui... 2 "; break;

				if (unlink($arquivo) && unlink($miniatura)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Prepara o texto do log para ser gravado.
	 */
	private function preparaLogExcluirFotos($vfotos, $vcliente, $vfilial, $cont) {

		// texto a ser gravado no log
		$texto = " \n *------------------------------------------------------------*";
		$texto .= " \n Data da ação: " . date("d/m/Y H:i:s");
		$texto .= " \n Ação: Foto Excluída do FTP e do Banco de Dados.";
		$texto .= " \n Pk da foto: " . $vfotos[$cont] -> getPkFoto();
		$texto .= " \n URL: " . $vfotos[$cont] -> getcaminhoFoto() . "/" . $vfotos[$cont] -> getFoto() . "." . $vfotos[$cont] -> getTipo();
		$texto .= " \n Data Vencimento: " . $vfotos[$cont] -> getDtVencimento();
		$texto .= " \n	Loja: " . $vfotos[$cont] -> getLoja();

		$texto .= " \n	Cliente: " . $vcliente -> getNomeFantasia();

		$texto .= " \n	Filial: " . $vfilial -> getFilial();

		$this -> caminho = $vfotos[$cont] -> getcaminhoFoto();
		$this -> caminhoFoto = $vfotos[$cont] -> getcaminhoFoto() . "/" . $vfotos[$cont] -> getFoto() . "." . $vfotos[$cont] -> getTipo();
		$this -> caminhoMiniatura = $vfotos[$cont] -> getMiniatura();

		$acao = "excluirFotos";

		// grava log
		$this -> gravaLog($texto, $acao);

		return true;
	}

	/*== DESATIVAR FOTOS ===============================================================================*/

	public function desativarFotosAction() {

		$objeto = new DesativaFotosModel();

		//Listando as fotos com a data de vencimento igual a data atual.
		$vfotos = $objeto -> listaFotosVencimento();

		if (count($vfotos) > 0) {
			$cont = -1;
			// para cada foto encontrada, busca os dados de cliente e filial da foto.
			foreach ($vfotos as $item) {
				$cont++;
				// pega pk da foto atual
				$idFoto = $item -> getPkFoto();

				// busca dados do cliente vinculado a foto atual
				$vcliente = $objeto -> listaDadosCliente($idFoto);

				// busca dados da filial vinculada a foto atual
				$vfilial = $objeto -> listaDadosFilial($idFoto);

				// faz a gravação dos dados em log
				if ($this -> preparaLogDesativarFotos($vfotos, $vcliente, $vfilial, $cont)) {

					// atualiza o registro da foto
					if ($objeto -> desativaFoto($idFoto)) {
						$this -> alteracoes++;
					} else {
						$this -> erros++;
					}
				}
			}

			$vetor = array('erros' => $this -> erros, 'alteracoes' => $this -> alteracoes);

			$this -> redirToView($vetor);
		} else {
			$this -> resultado = "Nenhum arquivo encontrado.";

			$vetor = array('resultado' => $this -> resultado);

			$this -> redirToView($vetor);
		}
	}

	/**
	 * Prepara o texto do log para ser gravado.
	 */
	private function preparaLogDesativarFotos($vfotos, $vcliente, $vfilial, $cont) {

		// texto a ser gravado no log
		$texto = " \n *------------------------------------------------------------*";
		$texto .= " \n Data da ação: " . date("d/m/Y H:i:s");
		$texto .= " \n Ação: Foto Desativada/Despublicada";
		$texto .= " \n Pk da foto: " . $vfotos[$cont] -> getPkFoto();
		$texto .= " \n URL: " . $vfotos[$cont] -> getcaminhoFoto() . "/" . $vfotos[$cont] -> getFoto() . "." . $vfotos[$cont] -> getTipo();
		$texto .= " \n Data Vencimento: " . $vfotos[$cont] -> getDtVencimento();
		$texto .= " \n	Loja: " . $vfotos[$cont] -> getLoja();

		$texto .= " \n	Cliente: " . $vcliente -> getNomeFantasia();

		$texto .= " \n	Filial: " . $vfilial -> getFilial();

		$acao = "desativarFotos";

		// grava log
		$this -> gravaLog($texto, $acao);

		return true;
	}

	/*== NOTIFICAR FOTOS ===============================================================================*/

	/**
	 * Seleciona os clientes que serão avisados.
	 */
	public function notificarFotosAction() {
		$objFotos = new NotificaFotosModel();

		$vDestinos = $objFotos -> selecionaDestinatarios();

		// echo "<pre>";
		// print_r($vDestinos);
		// echo "</pre>";
		// break;
		
		$vetor = array();

		foreach ($vDestinos as $destino) {
			// se enviou o email corretamente ao destinatário, faz as
			// atualizações dos registros do cliente e fotos.
			if ($this -> enviarEmail($destino -> getNomeUsuario(), $destino -> getEmailUsuario())) {

				// atualiza o campo 'notificar', na tabela 'foto', das fotos do cliente atual
				if (!$objFotos -> atualizaFotos($destino -> getIdCliente())) {
					
					$this -> resultado = "Erro ao atualizar as fotos do cliente " . $destino -> getIdCliente();
					$vetor = array('resultado' => $this -> resultado);
					$this -> redirToView($vetor);
					
				} else {
					// atualiza o campo 'notificarFotos' da tabela 'cliente'
					if (!$objFotos->atualizaClientesFotos($destino -> getIdCliente())) {
						
						$this -> resultado = "Erro ao atualizar o registro do cliente " . $destino -> getIdCliente();
						$vetor = array('resultado' => $this -> resultado);
						$this -> redirToView($vetor);
						
					}
				}

				// gravar log em txt
				
			} else {
				// grava erro no envio do email no log.
				$this -> resultado = "Erro ao atualizar o registro do cliente " . $destino -> getIdCliente();
				$vetor = array('resultado' => $this -> resultado);
				$this -> redirToView($vetor);
			}
		}

		$this -> resultado = "Os clientes foram notificados e os registros atualizados corretamente.";
		$vetor = array('resultado' => $this -> resultado);
		$this -> redirToView($vetor);
	}
	
	/**
	 * Seleciona os clientes que serão avisados.
	 */
	public function notificarArquivosAction() {
		$objFotos = new NotificaArquivosModel();

		$vDestinos = $objFotos -> selecionaDestinatarios();

		// echo "<pre>";
		// print_r($vDestinos);
		// echo "</pre>";
		// break;
		
		$vetor = array();

		foreach ($vDestinos as $destino) {
			// se enviou o email corretamente ao destinatário, faz as
			// atualizações dos registros do cliente e fotos.
			if ($this -> enviarEmail($destino -> getNomeUsuario(), $destino -> getEmailUsuario())) {

				// atualiza o campo 'notificar', na tabela 'foto', das fotos do cliente atual
				if (!$objFotos -> atualizaArquivos($destino -> getIdCliente())) {
					
					$this -> resultado = "Erro ao atualizar as fotos do cliente " . $destino -> getIdCliente();
					$vetor = array('resultado' => $this -> resultado);
					$this -> redirToView($vetor);
					
				} else {
					// atualiza o campo 'notificarFotos' da tabela 'cliente'
					if (!$objFotos->atualizaClientesArquivos($destino -> getIdCliente())) {
						
						$this -> resultado = "Erro ao atualizar o registro do cliente " . $destino -> getIdCliente();
						$vetor = array('resultado' => $this -> resultado);
						$this -> redirToView($vetor);
						
					}
				}

				// gravar log em txt
				
			} else {
				// grava erro no envio do email no log.
				$this -> resultado = "Erro ao atualizar o registro do cliente " . $destino -> getIdCliente();
				$vetor = array('resultado' => $this -> resultado);
				$this -> redirToView($vetor);
			}
		}

		$this -> resultado = "Os clientes foram notificados e os registros atualizados corretamente.";
		$vetor = array('resultado' => $this -> resultado);
		$this -> redirToView($vetor);
	}
	
	/**
	 * Monta a mensagem para enviar o email
	 */
	private function enviarEmail($nomeDestino, $emailDestino) {

		$nomeRemetente = "Dinamica RH e Merchandising";
		$emailRementente = "dinamicarh@dinamicarh.net";

		$assunto = "Novas atualizações de PDV";

		$headers = "From: " . $nomeRemetente . " <" . $emailRemetente . ">\nContent-type: text/html; charset=iso-8859-1\n";

		$msgEmail = "Prezado(a) " . $nomeDestino . "<br /><br />";
		$msgEmail .= "Novas fotos/relatórios estão disponíveis no sistema. Para visualiza-las, acesse a URL abaixo.<br /><br />";

		$msgEmail .= "<a href='http://dinamicarh.net/arquivos_testar/?controle=Inicio&acao=logar'>Sistema Diagnóstico</a>";
		
		$msgEmail .= "<br /><br />Dinâmica RH & Merchandising";
		
		$msgEmail .= "<br /><br /><strong>Atenção:</strong> Essa é uma mensagem automática. Para respondê-la, utilize o endereço <br />de email do gerente da filial Dinâmica com a qual tem contrato vinculado.";

		$email = mail("$emailDestino", "$assunto", "$msgEmail", "$headers");

		if ($email) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>