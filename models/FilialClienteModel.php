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
 * Arquivo - FilialClienteModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class FilialClienteModel extends PersistModelAbstract {
 	
	private $pkFilialCliente;
	
	// cliente
	private $pkdoCliente;
	private $nomedoCliente;
	private $pastaCliente;
	
	// filial
	private $pkdaFilial;
	private $nomedaFilial;
	private $pastaFilial;
	
	// 
	private $vinculoStatus;
	private $vinculoStatusAlterado;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	
	public function getPkFilialCliente() {
		return $this->pkFilialCliente;
	}
	
	public function setPkFilialCliente($idFilialCliente) {
		$this->pkFilialCliente = $idFilialCliente;
		return $this;
	}
	
	public function getPkdoCliente() {
		return $this->pkdoCliente;
	}
	
	public function setPkdoCliente($idCliente) {
		$this->pkdoCliente = $idCliente;
		return $this;
	}
	 
	public function getNomedoCliente() {
	 	return $this->nomedoCliente;
	}
	 
	public function setNomedoCliente($cliente) {
	 	$this->nomedoCliente = $cliente;
		return $this;
	}
	
	public function getPastaCliente() {
		return $this->pastaCliente;
	}
	
	public function setPastaCliente($pasta) {
		$this->pastaCliente = $pasta;
		return $this;
	}
	 
	public function getPkdaFilial() {
		return $this->pkdaFilial;
	}
	
	public function setPkdaFilial($idFilial) {
		$this->pkdaFilial = $idFilial;
		return $this;
	}
	
	public function getNomedaFilial() {
		return $this->nomedaFilial;
	}
	
	public function setNomedaFilial($filial) {
		$this->nomedaFilial = $filial;
		return $this;
	}
	
	public function getVinculoStatus() {
	  return $this->vinculoStatus;
	}
	
	public function setVinculoStatus($status) {
	  $this->vinculoStatus = $status;
	  return $this;
	}
	
	public function getVinculoStatusAlterado() {
	  return $this->vinculoStatusAlterado;
	}
	
	public function setVinculoStatusAlterado($alterado) {
	  $this->vinculoStatusAlterado = $alterado;
	  return $this;
	}
	
	public function getPastaFilial() {
	  return $this->pastaFilial;
	}
	
	public function setPastaFilial($pastaFilial) {
	  $this->pastaFilial = $pastaFilial;
	  return $this;
	}

	/**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null, $cliente = "", $filial = 0) {
			
		if (!is_null($id)) {
			$st_query = "SELECT cliente.*, filial.*, filialCliente.* FROM cliente
				    INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
				    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial
					    WHERE filialCliente.pk_filialCliente = ".$id." 
					 ORDER BY filialCliente.pk_filialCliente ASC;";
		} else {
			$st_query = "SELECT cliente.*, filial.*, filialCliente.* 
						FROM cliente
				    INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
				    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial";
				    
			if ($_SESSION['hashid'] == "gerentefilial") {
				$st_query .= " AND filial.pk_filial = ".$_SESSION['idFilial'];
			}
			
			//FILTROS
			// Filtro por cliente
			if ($cliente != "") {
				$st_query .= " AND cliente.fantasia LIKE '%".$cliente."'";
			}
			
			// Filtro por filial
			if ($filial != 0) {
				$st_query .= " AND filial.pk_filial = ".$filial;
			}
			
			$st_query .= " ORDER BY filialCliente.pk_filialCliente ASC;";
		}
		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new FilialClienteModel();
				
				$objeto->setPkFilialCliente($o_ret->pk_filialCliente);
				$objeto->setPkdoCliente($o_ret->pk_cliente);
				$objeto->setNomedoCliente($o_ret->fantasia);
				$objeto->setPkdaFilial($o_ret->filial_pk_filial);
				$objeto->setNomedaFilial($o_ret->filial);
				$objeto->setVinculoStatus($o_ret->vinculoStatus);
				$objeto->setVinculoStatusAlterado($o_ret->vinculoStatusAlterado);
				$objeto->setPastaFilial($o_ret->pastaFilial);
				
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
		//print "id: ".$id; break;
		
		$vetor = array();
		$st_query = "SELECT cliente.*, filial.*, filialCliente.* FROM cliente
					INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
					INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial
					WHERE filialCliente.pk_filialCliente =".$id.";";
		
		//print $st_query; break;
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkFilialCliente($o_ret->pk_filialCliente);
			$this->setPkdoCliente($o_ret->pk_cliente);
			$this->setNomedoCliente($o_ret->fantasia);
			$this->setPkdaFilial($o_ret->filial_pk_filial);
			$this->setNomedaFilial($o_ret->filial);
			$this->setVinculoStatus($o_ret->vinculoStatus);
			$this->setVinculoStatusAlterado($o_ret->vinculoStatusAlterado);
			$this->setPastaFilial($o_ret->pastaFilial);
			
			return $this;
		}
		catch(PDOException $e)
		{}
		return false;
	}

	/**
	* Retorna os dados referente
	* a um determinado Id Filial e Id Cliente
	*/
	public function listaFilialCliente($idCliente,$idFilial) {
		//print "idCliente: ".$idCliente." idFilial: ".$idFilial; break;
		
		$vetor = array();
		$st_query = "SELECT filialCliente.*
					FROM filialCliente
					WHERE filialCliente.cliente_pk_cliente = ".$idCliente."
					  AND filialCliente.filial_pk_filial = ".$idFilial;
		
		//print $st_query; break;
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkFilialCliente($o_ret->pk_filialCliente);
			$this->setPkdoCliente($o_ret->pk_cliente);
			//$this->setNomedoCliente($o_ret->fantasia);
			$this->setPkdaFilial($o_ret->filial_pk_filial);
			//$this->setNomedaFilial($o_ret->filial);
			$this->setVinculoStatus($o_ret->vinculoStatus);
			$this->setVinculoStatusAlterado($o_ret->vinculoStatusAlterado);
			$this->setPastaFilial($o_ret->pastaFilial);
			
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
		//print "pkFilialCliente: ".$this->getPkFilialCliente(); break;
		
		if (is_null($this->getPkFilialCliente())) {
		//if ($this->loadById($id)) {
			// verifica se já existe um registro com os dados repassados
			$st_query = "SELECT * FROM filialCliente 
						WHERE cliente_pk_cliente = ".$this->getPkdoCliente()."
						AND filial_pk_filial = ".$this->getPkdaFilial();
			
			$o_data = $this->o_db->query($st_query);
			
			if ($o_data->fetchObject() > 0) {
				return -1;
			}
			
			$st_query = "INSERT INTO filialCliente
							(pk_filialCliente,
							filial_pk_filial,
							cliente_pk_cliente,
							vinculoStatus,
							vinculoStatusAlterado,
							pastaFilial)
					   VALUES ('$this->pkFilialCliente',
							'$this->pkdaFilial',
							'$this->pkdoCliente',
							'$this->vinculoStatus',
							'$this->vinculoStatusAlterado',
							'$this->pastaFilial');";
		
		//print $st_query; break;
		
		} else {
			//print $this->pkFilialUsuario; break;
			
			$st_query = "UPDATE filialCliente 
						 SET	filial_pk_filial = '$this->pkdaFilial',
							cliente_pk_cliente = '$this->pkdoCliente',
							vinculoStatus = '$this->vinculoStatus',
							vinculoStatusAlterado = '$this->vinculoStatusAlterado'							
					    WHERE pk_filialCliente = '$this->pkFilialCliente'";
					    
		//pastaFilial = '$this->pastaFilial'
		
		//print $st_query; break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->PkFilialCliente)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_filialCliente')->fetchObject();
					return $o_ret->pk_filialCliente;
				} else {
					return $this->PkFilialCliente;
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
		
		if (!is_null($this->pkFilialCliente)) {
				
			// if ($this->vinculoStatus == 1) {
				// $status = 1;
			// } else {
				// $status = 0;
			// }
			
			$status = ($this->vinculoStatus == 1) ? 2 : 1 ;
			
			$agora = date("Y-m-d");
			
			$st_query = "UPDATE filialCliente
						 SET vinculoStatus = '$status',
						 	vinculoStatusAlterado = '$agora'
					    WHERE pk_filialCliente = $this->pkFilialCliente";
			
			if ($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Deleta os dados persistidos na tabela de
	* perfil usando como referencia, o id da classe.
	*/
	public function delete() {
		
		if (!is_null($this->pkFilialCliente)) {
			$st_query = "DELETE FROM filialCliente
						WHERE pk_filialCliente = $this->pkFilialCliente";
			
			if ($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
