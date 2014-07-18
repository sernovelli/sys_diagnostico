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
 * Arquivo - CidadeModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class ClienteModel extends PersistModelAbstract {

	private $pkCliente;
	private $contrato;
	private $nomeFantasia;
	private $razaoSocial;
	private $cnpj;
	private $nomePasta;
	private $idCidade;
	private $nomeCidade;
	private $clienteStatus;
	private $dataModificado;
	private $idFilial;
	private $notificaFotos;
	private $notificaArquivos;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	
	public function getPkCliente() {
	 	return $this->pkCliente;
	}
	 
	public function setPkCliente($pkCliente) {
	 	$this->pkCliente = $pkCliente;
		return $this;
	}
	 
	public function setContrato($nContrato) {
		$this->contrato = $nContrato;
		return $this;
	}
	
	public function getContrato() {
		return $this->contrato;
	}
	
	public function setNomeFantasia($nFantasia) {
		$this->nomeFantasia = $nFantasia;
		return $this;
	}
	
	public function getNomeFantasia() {
		return $this->nomeFantasia;
	}
	
	public function setRazaoSocial($rSocial) {
		$this->razaoSocial = $rSocial;
		return $this;
	}
	
	public function getRazaoSocial() {
		return $this->razaoSocial;
	}
	
	public function getCnpj() {
		return $this->cnpj;
	}
	
	public function setCnpj($cnpj) {
		$this->cnpj = $cnpj;
		return $this;
	}
	
	public function getIdCidade() {
		return $this->idCidade;
	}
	
	public function setIdCidade($idCidade) {
		$this->idCidade = $idCidade;
		return $this;
	}
	 
	public function getNomeCidade() {
		return $this->nomeCidade;
	}
	
	public function setNomeCidade($nCidade) {
		$this->nomeCidade = $nCidade;
		return $this;
	}
	
	public function getNomePasta() {
		return $this->nomePasta;
	}
	
	public function setNomePasta($pasta) {
		$this->nomePasta = $pasta;
		return $this;
	}
	
	public function getClienteStatus() {
		return $this->clienteStatus;
	}
	
	public function setClienteStatus($status) {
		$this->clienteStatus = $status;
		return $this; 
	}
	
	public function getDataModificado() {
		return $this->dataModificado;
	}
	
	public function setDataModificado($data) {
		$this->dataModificado = $data;
		return $this;
	}
	
	public function getIdFilial() {
	  return $this->idFilial;
	}
	
	public function setIdFilial($idFilial) {
	  $this->idFilial = $idFilial;
	  return $this;
	}
	
	public function getNotificaFotos() {
	  return $this->notificaFotos;
	}
	
	public function setNotificaFotos($notificar) {
	  $this->notificaFotos = $notificar;
	  return $this;
	}
	
	public function getNotificaArquivos() {
	  return $this->notificaArquivos;
	}
	
	public function setNotificaArquivos($notifica) {
	  $this->notificaArquivos = $notifica;
	  return $this;
	}
	
	/**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null, $status = 1, $nFantasia = "", $cnpj = "", $contrato = "") {
			
		if (!is_null($id)) {
			$st_query = "SELECT cliente.*, cidade.* FROM cliente
						INNER JOIN cidade ON cliente.cidade_pk_cidade = cidade.pk_cidade
						WHERE cliente.pk_cliente = ".$id."
						  AND cliente.clienteStatus = '1'
						ORDER BY cliente.pk_cliente ASC";
		} else {
			$st_query = "SELECT cliente.*, cidade.*
					     FROM cliente
				    INNER JOIN cidade ON cliente.cidade_pk_cidade = cidade.pk_cidade";
			
			if ($_SESSION['hashid'] == "gerentefilial") {
				
				$st_query = "SELECT cliente.*, cidade.*, filialCliente.*, filial.*
						     FROM cliente
					    INNER JOIN cidade ON cliente.cidade_pk_cidade = cidade.pk_cidade
					    INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
					    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
					    		 AND filial.pk_filial = ".$_SESSION['idFilial'];
			}
			
			//FILTROS
			// Filtro por nome fantasia
			if ($nFantasia != "") {
				$st_query .= " AND cliente.fantasia LIKE '%".$nFantasia."%'";
			}
			
			// filtro por CNPJ
			if ($cnpj != "") {
				$st_query .= " AND cliente.cnpj LIKE '".$cnpj."'";
			}
			
			// filtro por contrato
			if ($contrato != "") {
				$st_query .= " AND cliente.contrato LIKE '".$contrato."'";
			}
			
			// filtro por status
			if ($status != 0) {
				$st_query .= " AND cliente.clienteStatus = ".$status;
			}
			
			$st_query .= " ORDER BY cliente.pk_cliente ASC";
		}
		
		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new ClienteModel();
				
				$objeto->setPkCliente($o_ret->pk_cliente);
				$objeto->setContrato($o_ret->contrato);
				$objeto->setNomeFantasia($o_ret->fantasia);
				$objeto->setRazaoSocial($o_ret->social);
				$objeto->setCnpj($o_ret->cnpj);
				$objeto->setNomePasta($o_ret->nomePasta);
				$objeto->setIdCidade($o_ret->cidade_pk_cidade);
				$objeto->setNomeCidade($o_ret->cidade);
				$objeto->setClienteStatus($o_ret->clienteStatus);
				$objeto->setDataModificado($o_ret->clienteStatusAlterado);
				$objeto->setNotificaFotos($o_ret->notificarFotos);
				$objeto->setNotificaArquivos($o_ret->notificarArquivos);
				
				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}
	
	 /**
	  * Retorna um array contendo os registros
	  * do cliente de acordo com a filial do gerente logado.
	  */
	public function listarClientePorFilial($id) {
			
		if (!is_null($id))
			 $st_query = "SELECT cliente.*, filialCliente.*, filial.* 
						 FROM cliente
						INNER JOIN filialCliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente
						INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial
						WHERE filial.pk_filial = ".$_SESSION['idFilial']."

						  AND cliente.clienteStatus = 1";
		
		//print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new ClienteModel();
				$objeto->setPkCliente($o_ret->pk_cliente);
				$objeto->setContrato($o_ret->contrato);
				$objeto->setNomeFantasia($o_ret->fantasia);
				$objeto->setRazaoSocial($o_ret->social);
				$objeto->setCnpj($o_ret->cnpj);
				//$objeto->setNomePasta($o_ret->nomePasta);
				//$objeto->setIdCidade($o_ret->cidade_pk_cidade);
				//$objeto->setNomeCidade($o_ret->cidade);
				$objeto->setClienteStatus($o_ret->clienteStatus);
				$objeto->setDataModificado($o_ret->clienteStatusAlterado);
				$objeto->setIdFilial($o_ret->pk_filial);
				//$objeto->setNotificaFotos($o_ret->notificarFotos);
				//$objeto->setNotificaArquivos($o_ret->notificarArquivos);
				
				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}
	
	
	
	/**
	* Retorna os dados de um perfil referente
	* a um determinado Id
	*/
	public function loadById($id) {
		
		$vetor = array();
		$st_query = "SELECT cliente.*, cidade.* FROM cliente
						INNER JOIN cidade ON cliente.cidade_pk_cidade = cidade.pk_cidade
						WHERE cliente.pk_cliente = ".$id.";";
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkCliente($o_ret->pk_cliente);
			$this->setContrato($o_ret->contrato);
			$this->setNomeFantasia($o_ret->fantasia);
			$this->setRazaoSocial($o_ret->social);
			$this->setCnpj($o_ret->cnpj);
			$this->setNomePasta($o_ret->nomePasta);
			$this->setIdCidade($o_ret->cidade_pk_cidade);
			$this->setNomeCidade($o_ret->cidade);
			$this->setClienteStatus($o_ret->clienteStatus);
			$this->setDataModificado($o_ret->clienteStatusAlterado);
			$this->setNotificaFotos($o_ret->notificarFotos);
			$this->setNotificaArquivos($o_ret->notificarArquivos);
			
			return $this;
		}
		catch(PDOException $e)
		{}
		return false;
	}

	
	/**
	* Salva dados contidos na instancia da classe
	* na tabela de perfil. Se o ID for passado,
	* um UPDATE ser� executado, caso contr�rio, um
	* INSERT ser� executado
	*/
	public function save() {
		$agora = date("Y-m-d");
		
		if (is_null($this->pkCliente)) {
				
			$st_query = "INSERT INTO cliente
							(pk_cliente,
							contrato,
							fantasia,
							social,
							cnpj,
							nomePasta,
							clienteStatus,
							clienteStatusAlterado,
							cidade_pk_cidade,
							notificarFotos,
							notificarArquivos)
					   VALUES ('$this->pkCliente',
							'$this->contrato',
							'$this->nomeFantasia',
							'$this->razaoSocial',
							'$this->cnpj',
							'$this->nomePasta',
							'1',
							'$agora',
							'$this->idCidade',
							'$this->notificaFotos',
							'$this->notificaArquivos');";
		
		//echo $st_query; break;
		} else {
				
			//print "status: ".$this->clienteStatus." modificado em: ".$this->dataModificado; break;

			$st_query = "UPDATE cliente 
						 SET	contrato = '$this->contrato',
							fantasia = '$this->nomeFantasia',
							social = '$this->razaoSocial',
							cnpj = '$this->cnpj',
							nomePasta = '$this->nomePasta',
							clienteStatus = '$this->clienteStatus',
							clienteStatusAlterado = '$agora',
							cidade_pk_cidade = '$this->idCidade',
							notificarFotos = '$this->notificaFotos',
							notificarArquivos = '$this->notificaArquivos'
					    WHERE pk_cliente = '$this->pkCliente'";
		
		//print $st_query; break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkCliente)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_cliente')->fetchObject();
					return $o_ret->pk_cliente;
				} else {
					return $this->pkCliente;
				}
		}
		catch (PDOException $e) {
			throw $e;
		}
		return false;
	}

	/**
	 * Ativa/desativa os dados persistidos na tabela
	 * usando como referencia, o id da classe.
	 */
	public function desativa() {
		
		if (!is_null($this->pkCliente)) {
			
			$status = ($this->vinculoStatus == 1) ? 2 : 1 ;
			
			$agora = date("Y-m-d");
			
			$st_query = "UPDATE cliente 
						 SET clienteStatus = '$status',
						 	clienteStatusAlterado = '$agora'
					    WHERE pk_cliente = $this->pkCliente";
			
			//print $st_query; break;
			
			if ($this->o_db->exec($st_query) > 0) {
				return true;
			}
			return false;
		}
	}
	
	/**
	* Deleta os dados persistidos na tabela de
	* perfil usando como referencia, o id da classe.
	*/
	public function delete() {
		
		if (!is_null($this->pkCliente)) {
			$st_query = "DELETE FROM cliente
						WHERE pk_cliente = $this->pkCliente";
			
			if ($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }

?>
