<?php
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
 class CidadeModel extends PersistModelAbstract {
 	
	private $pkCidade;
	private $cidade;
	private $pkEstado;
	private $estado;
	private $sigla;

	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	 
	 public function getPkCidade() {
	 	return $this->pkCidade;
	 }
	 
	 public function setPkCidade($pkCidade) {
	 	$this->pkCidade = $pkCidade;
		return $this;
	 }
	 
	 public function getCidade() {
	 	return $this->cidade;
	 }
	 
	 public function setCidade($cidade) {
	 	$this->cidade = $cidade;
		return $this;
	 }
	 
	 public function getPkEstado() {
	 	return $this->pkEstado;
	 }
	 
	 public function setPkEstado($idEstado) {
	 	$this->pkEstado = $idEstado;
		return $this;
	 }
	 
	 public function getEstado() {
	 	return $this->estado;
	 }
	 
	 public function setEstado($estado) {
	 	$this->estado = $estado;
		return $this;
	 }
	 
	 public function getSigla() {
	 	return $this->sigla;
	 }
	 
	 public function setSigla($uf) {
	 	$this->sigla = $uf;
		return $this;
	 }


	 /**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null, $cidade = "", $estado = 0) {
			
		if (!is_null($id)) {
			$st_query = "SELECT cidade.*, estado.* FROM cidade
						INNER JOIN estado ON cidade.estado_pk_estado = estado.pk_estado
						WHERE cidade.pk_cidade = ".$id." 
						GROUP BY cidade.pk_cidade
						ORDER BY cidade.pk_cidade, cidade.estado_pk_estado ASC;";
		} else {
			$st_query = "SELECT cidade.*, estado.* FROM cidade
						INNER JOIN estado ON cidade.estado_pk_estado = estado.pk_estado";
		}
		
		// FILTROS
		// Filtro por cidade
		if ($cidade != "") {
			$st_query .= " AND cidade.cidade LIKE '%".$cidade."%'";
		}
		
		// Filtro por estado
		if ($estado != 0) {
			$st_query .= " AND estado.pk_estado = ".$estado;
		}
		
		$st_query .= " GROUP BY cidade.pk_cidade
					   
					ORDER BY cidade.pk_cidade, cidade.estado_pk_estado ASC";
		
		// Limita a qtde de registros se não houverem filtros.
		if ($cidade == "" && $estado == 0) {
			$st_query .= " LIMIT 100"; 
		}
		
		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new CidadeModel();
				
				$objeto->setPkCidade($o_ret->pk_cidade);
				$objeto->setCidade($o_ret->cidade);
				$objeto->setPkEstado($o_ret->estado_pk_estado);
				$objeto->setEstado($o_ret->estado);
				$objeto->setSigla($o_ret->sigla);
				
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
		$st_query = "SELECT cidade.*, estado.* FROM cidade
					INNER JOIN estado ON cidade.estado_pk_estado = estado.pk_estado
					WHERE cidade.pk_cidade = ".$id.";";
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			$this->setPkCidade($o_ret->pk_cidade);
			$this->setCidade($o_ret->cidade);
			$this->setPkEstado($o_ret->estado_pk_estado);
			$this->setEstado($o_ret->estado);
			
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
	
		if (is_null($this->pkCidade)) {
			$st_query = "INSERT INTO cidade
							(pk_cidade,
							cidade,
							estado_pk_estado)
					   VALUES ('$this->pkCidade',
							'$this->cidade',
							'$this->pkEstado');";
		
		//echo $st_query; break;
		} else {
			$st_query = "UPDATE cidade 
						 SET	cidade = '$this->cidade',
							estado_pk_estado = '$this->pkEstado'
					    WHERE pk_cidade = '$this->pkCidade'";
		
		//echo $st_query; break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkCidade)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_cidade')->fetchObject();
					return $o_ret->pk_cidade;
				} else {
					return $this->pkCidade;
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
		
		if (!is_null($this->pkCidade)) {
			$st_query = "DELETE FROM cidade
						WHERE pk_cidade = $this->pkCidade";
			
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
