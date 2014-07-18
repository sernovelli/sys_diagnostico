<?php
/**
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
 class FilialModel extends PersistModelAbstract {
 	
	private $pkFilial;
	private $filial;
	private $idCidade;
	private $nomeCidade;
	
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	
	public function getPkFilial() {
		return $this->pkFilial;
	}
	
	public function setPkFilial($idFilial) {
		$this->pkFilial = $idFilial;
		return $this;
	}
	 
	public function getFilial() {
	 	return $this->filial;
	}
	 
	public function setFilial($filial) {
	 	$this->filial = $filial;
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
	

	/**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null) {
			
		if (!is_null($id))
			$st_query = "SELECT filial.*, cidade.* FROM filial
						INNER JOIN cidade ON cidade.pk_cidade = filial.cidade_pk_cidade
						WHERE filial.pk_filial = ".$id." 
						ORDER BY filial.pk_filial ASC;";
		else
			$st_query = 'SELECT filial.*, cidade.* FROM filial
						INNER JOIN cidade ON cidade.pk_cidade = filial.cidade_pk_cidade
						ORDER BY filial.pk_filial ASC;';
		
		//print $st_query;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new FilialModel();
				
				$objeto->setPkFilial($o_ret->pk_filial);
				$objeto->setFilial($o_ret->filial);
				$objeto->setIdCidade($o_ret->cidade_pk_cidade);
				$objeto->setNomeCidade($o_ret->cidade);
				
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
		$st_query = "SELECT filial.*, cidade.* FROM filial
						INNER JOIN cidade ON cidade.pk_cidade = filial.cidade_pk_cidade
						WHERE filial.pk_filial = ".$id.";";
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkFilial($o_ret->pk_filial);
			$this->setFilial($o_ret->filial);
			$this->setIdCidade($o_ret->cidade_pk_cidade);
			$this->setNomeCidade($o_ret->cidade);
			
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
	
		if (is_null($this->pkFilial)) {
			$st_query = "INSERT INTO filial
							(pk_filial,
							filial,
							cidade_pk_cidade)
					   VALUES ('$this->pkFilial',
							'$this->filial',
							'$this->idCidade');";
		
		//echo $st_query; break;
		} else {
			$st_query = "UPDATE filial 
						 SET	filial = '$this->filial',
							cidade_pk_cidade = '$this->idCidade'
					    WHERE pk_filial = '$this->pkFilial'";
		
		//echo $st_query; break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkFilial)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_filial')->fetchObject();
					return $o_ret->pk_filial;
				} else {
					return $this->pkFilial;
				}
		}
		catch (PDOException $e) {
			throw $e;
		}
		return false;
	}

	/**
	* Deleta os dados persistidos na tabela de
	* perfil usando como referencia, o id da classe.
	*/
	public function delete() {
		
		if (!is_null($this->pkFilial)) {
			$st_query = "DELETE FROM filial
						WHERE pk_filial = $this->pkFilial";
			
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	/**
	* Retorna um array contendo os registros das filiais
	* para as quais o usuario logado tem acesso.
	* Utilizado nos selects de filtros.
	*/
	public function listaFiliaisPorUsuario($id = null) {
			
		$st_query = "SELECT filial.*, filialUsuario.*, usuario.*
					FROM filial
			    INNER JOIN filialUsuario ON filialUsuario.filial_pk_filial = filial.pk_filial
			    INNER JOIN usuario ON usuario.pk_usuario = filialUsuario.usuario_pk_usuario
			    		 AND filialUsuario.usuario_pk_usuario = '".$id."'
				 ORDER BY filial.pk_filial ASC";
		
		//print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new FilialModel();
				
				$objeto->setPkFilial($o_ret->pk_filial);
				$objeto->setFilial($o_ret->filial);
				$objeto->setIdCidade($o_ret->cidade_pk_cidade);
				$objeto->setNomeCidade($o_ret->cidade);
				
				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}
	
	/**
	* Retorna um array contendo os registros das filiais
	* nas quais o CLIENTE logado está vinculado.
	* Utilizado nos selects de filtros.
	*/
	public function listaFilialPorClienteLogado($id = null) {
			
		if (!is_null($id)) {
			$st_query = "SELECT cliente.*, filial.*, filialCliente.*, clienteUsuario.*, usuario.* 
						FROM cliente
				    INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
				    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial
				    INNER JOIN clienteUsuario ON clienteUsuario.cliente_pk_cliente = cliente.pk_cliente
				    INNER JOIN usuario ON clienteUsuario.usuario_pk_usuario = usuario.pk_usuario
				    		 AND usuario.pk_usuario = ".$id." 
				    	 ORDER BY filialCliente.pk_filialCliente ASC;";
		}
		
		//print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new FilialModel();
				
				$objeto->setPkFilial($o_ret->pk_filial);
				$objeto->setFilial($o_ret->filial);
				$objeto->setIdCidade($o_ret->cidade_pk_cidade);
				$objeto->setNomeCidade($o_ret->cidade);
				
				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}

 }
 
 ?>
