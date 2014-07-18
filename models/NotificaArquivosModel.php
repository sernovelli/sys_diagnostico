<?php
// inicia a sessao
ob_start();
session_start();

/**
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 *
 * Camada - Models
 * Diretorio Pai - models
 * Arquivo - NotificaArquivosModel.php
 *
 * @author Sergio Novelli
 * @version 1.0
 *
 */
class NotificaArquivosModel extends PersistModelAbstract {

	// cliente
	private $idCliente;
	private $nomeCliente;

	// usuario
	private $nomeUsuario;
	private $emailUsuario;

	function __construct() {
		parent::__construct();
	}

	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */

	// campos da tabela.
	// cliente
	public function getIdCliente() {
		return $this -> idCliente;
	}

	public function setIdCliente($idCliente) {
		$this -> idCliente = $idCliente;
		return $this;
	}

	public function getNomeCliente() {
		return $this -> nomeCliente;
	}

	public function setNomeCliente($nomeCliente) {
		$this -> nomeCliente = $nomeCliente;
		return $this;
	}

	// Usuarios
	public function getNomeUsuario() {
		return $this -> nomeUsuario;
	}

	public function setNomeUsuario($nomeusuario) {
		$this -> nomeUsuario = $nomeusuario;
		return $this;
	}

	public function getEmailUsuario() {
		return $this -> emailUsuario;
	}

	public function setEmailUsuario($emailusuario) {
		$this -> emailUsuario = $emailusuario;
		return $this;
	}

	/**
	 * Retorna um array contendo os registros
	 */
	public function selecionaDestinatarios() {
		// quantidade de fotos para notificar
		$numRegistros = $this -> contaRegistros("SELECT COUNT(arquivo.pk_arquivo) AS numArquivo FROM arquivo WHERE arquivo.notificar = '1' AND arquivo.arquivoStatus = 1");

		if ($numRegistros > 0) {
			// Seleciona os destinatários
			$st_query = "SELECT cliente.*, clienteUsuario.*, usuario.*
						FROM cliente
				    INNER JOIN clienteUsuario ON cliente.pk_cliente = clienteUsuario.cliente_pk_cliente
				    INNER JOIN usuario ON usuario.pk_usuario = clienteUsuario.usuario_pk_usuario
				    	    WHERE cliente.notificarArquivos = 1
				    	      AND cliente.clienteStatus = 1
				    	      AND usuario.usuarioStatus = 1";

			//echo "<pre>"; print $st_query; echo "</pre>"; break;

			try {
				$o_data = $this -> o_db -> query($st_query);

				$vetor = array();

				while ($o_ret = $o_data -> fetchObject()) {

					$objeto = new NotificaFotosModel();

					$objeto -> setIdCliente($o_ret -> pk_cliente);
					$objeto -> setNomeCliente($o_ret -> fantasia);
					$objeto -> setNomeUsuario($o_ret -> nome);
					$objeto -> setEmailUsuario($o_ret -> email);

					array_push($vetor, $objeto);
				}

			} catch(PDOException $e) {
			}
			return $vetor;
		}
	}

	/**
	 * Atualiza o campo 'notificar' de todos os arquivos que pertencem
	 * ao cliente passado por parametro.
	 */
	public function atualizaArquivos($idCliente) {
		require_once 'models/ArquivoModel.php';

		$query = "SELECT arquivo.*, filialCliente.*, cliente.* 
				  FROM arquivo
			 INNER JOIN filialCliente ON filialCliente.pk_filialCliente = arquivo.filialCliente_pk_filialCliente
			 INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente
			 	 WHERE cliente.pk_cliente = " . $idCliente ." 
			 	   AND arquivo.notificar = 1";

		$o_data = $this -> o_db -> query($query);

		while ($o_ret = $o_data -> fetchObject()) {
			$objeto = new ArquivoModel();
			$objeto -> setPkArquivo($o_ret -> pk_arquivo);

			$query2 = "UPDATE arquivo SET arquivo.notificar = '2' 
					  WHERE arquivo.notificar = '1' 
					    AND arquivo.pk_arquivo = " . $objeto -> getPkArquivo();

			if (!$this -> o_db -> exec($query2) > 0) {
				return FALSE;
			}
		} 
		return TRUE;
	}

	/**
	 * Método que atualiza o campo 'noficarArquivos' do cliente passado por parametro.
	 */
	public function atualizaClientesArquivos($idCliente) {

		$query = "UPDATE cliente SET cliente.notificarArquivos = '2' 
				 WHERE cliente.pk_cliente = " . $idCliente . " 
				   AND cliente.notificarArquivos = '1'";

		if (!$this -> o_db -> exec($query) > 0) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Método para calcular quantos registros há no total,
	 * sendo executado sem limitação de registros (LIMIT).
	 */
	private function contaRegistros($st_query) {
		//echo "<pre>"; print $st_query; echo "</pre>"; break;

		// executa a query sem limitação para gerar a paginação
		$o_data = $this -> o_db -> query($st_query);

		// Conta qtos registros a consulta retornou
		$o_ret = $o_data -> fetchAll(PDO::FETCH_ASSOC);
		$numRegistros = count($o_ret);
		
		return $numRegistros;
	}

}
?>
