<?php
// inicia a sessao
ob_start();
session_start();

/**
 *
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 *
 * Camada - Models
 * Diretorio Pai - models
 * Arquivo - NotificaFotosModel.php
 *
 * @author Sergio Novelli
 * @version 1.0
 *
 */
class NotificaFotosModel extends PersistModelAbstract {

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
		$numRegistros = $this -> contaRegistros("SELECT COUNT(foto.pk_foto) AS numFoto FROM foto WHERE foto.notificar = '1' AND foto.fotoStatus = 1");

		if ($numRegistros > 0) {
			// Seleciona os destinatários
			$st_query = "SELECT cliente.*, clienteUsuario.*, usuario.*
						FROM cliente
				    INNER JOIN clienteUsuario ON cliente.pk_cliente = clienteUsuario.cliente_pk_cliente
				    INNER JOIN usuario ON usuario.pk_usuario = clienteUsuario.usuario_pk_usuario
				    	    WHERE cliente.notificarFotos = 1
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
	 * Atualiza o campo 'notificar' de todas as fotos que pertencem
	 * ao cliente passado por parametro.
	 */
	public function atualizaFotos($idCliente) {
		require_once 'models/FotoModel.php';

		$query = "SELECT foto.*, filialCliente.*, cliente.* 
				  FROM foto
			 INNER JOIN filialCliente ON filialCliente.pk_filialCliente = foto.filialCliente_pk_filialCliente
			 INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente
			 	 WHERE cliente.pk_cliente = " . $idCliente ." 
			 	   AND foto.notificar = 1";

		$o_data = $this -> o_db -> query($query);

		while ($o_ret = $o_data -> fetchObject()) {

			$objeto = new FotoModel();
			$objeto -> setPkFoto($o_ret -> pk_foto);

			$query2 = "UPDATE foto SET foto.notificar = '2' 
					  WHERE foto.notificar = '1' 
					    AND foto.pk_foto = " . $objeto -> getPkFoto();

			if (!$this -> o_db -> exec($query2) > 0) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Método que atualiza o campo 'noficarFotos' do cliente passado por parametro.
	 */
	public function atualizaClientesFotos($idCliente) {

		$query = "UPDATE cliente SET cliente.notificarFotos = '2' 
				 WHERE cliente.pk_cliente = " . $idCliente . " 
				   AND cliente.notificarFotos = '1'";

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
