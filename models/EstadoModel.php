<?php
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Models
 * Diretorio Pai - models
 * Arquivo - EstadoModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class EstadoModel extends PersistModelAbstract {
 	
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
	 
	 public function getPkEstado() {
	 	return $this->pkEstado;
	 }
	 
	 public function setPkEstado($pkUf) {
	 	$this->pkEstado = $pkUf;
		return $this;
	 }
	 
	 public function getEstado() {
	 	return $this->estado;
	 }
	 
	 public function setEstado($uf) {
	 	$this->estado = $uf;
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
	public function _list($id = null) {
			
		if (!is_null($id))
			$st_query = "SELECT estado.* FROM estado
						WHERE estado.pk_estado = ".$id." 
						ORDER BY estado.pk_estado ASC;";
		else
			$st_query = 'SELECT estado.* FROM estado 
						ORDER BY estado.pk_estado ASC;';

		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new EstadoModel();
				
				$objeto->setPkEstado($o_ret->pk_estado);
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
		$st_query = "SELECT estado.* FROM estado
						WHERE estado.pk_estado = ".$id.";";
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkEstado($o_ret->pk_estado);
			$this->setEstado($o_ret->estado);
			$this->setSigla($o_ret->sigla);
			
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
			$st_query = "INSERT INTO estado
							(pk_estado,
							sigla)
					   VALUES ('$this->pkEstado',
							'$this->estado',
							'$this->sigla');";
		
		//echo $st_query; break;
		} else {
			$st_query = "UPDATE estado 
						 SET	estado = '$this->estado',
							sigla = '$this->sigla'
					    WHERE pk_estado = '$this->pkEstado'";
		
		//echo $st_query; break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkEstado)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_estado')->fetchObject();
					return $o_ret->pk_estado;
				} else {
					return $this->pkEstado;
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
		
		if (!is_null($this->pkEstado)) {
			$st_query = "DELETE FROM estado
						WHERE pk_estado = $this->pkEstado";
			
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
