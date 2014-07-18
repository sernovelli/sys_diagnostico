<?php
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Models
 * Diretorio Pai - models
 * Arquivo - PastaModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class PastaModel extends PersistModelAbstract {
 	
	private $pkPasta;
	private $pasta;
	private $url;
	private $pastaStatus;
	private $idPastaPai;
	private $idFilialCliente;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	
	public function getPkPasta() {
		return $this->pkPasta;
	}
	
	public function setPkPasta($idPasta) {
		$this->pkPasta = $idPasta;
		return $this;
	}
	
	public function getPasta() {
		return $this->pasta;
	}
	
	public function setPasta($pasta) {
		$this->pasta = $pasta;
		return $this;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}
	
	public function getPastaStatus() {
		return $this->pastaStatus;
	}
	
	public function setPastaStatus($status) {
		$this->pastaStatus = $status;
		return $this;
	}
	
	public function getIdPastaPai() {
		return $this->pastaPai;
	}
	
	public function setIdPastaPai($idPastaPai) {
		$this->idPastaPai = $idPastaPai;
		return $this;
	}
	
	public function getFilialCliente() {
		return $this->idFilialCliente;
	}
	
	public function setFilialCliente($idFilialCliente) {
		$this->idFilialCliente = $idFilialCliente;
		return $this;
	}

	
	 /**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null) {
			
		if (!is_null($id))
			$st_query = "SELECT pasta.*, filialCliente.* FROM pasta
						INNER JOIN filialCliente ON pasta.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente
						WHERE pasta.pk_pasta = ".$id." 
						ORDER BY pasta.pk_pasta ASC;";
		else
			$st_query = 'SELECT pasta.*, filialCliente.* FROM pasta
						INNER JOIN filialCliente ON pasta.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente 
						ORDER BY pasta.pk_pasta ASC;';
		
		//print $st_query;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new PastaModel();
				
				$objeto->setPkPasta($o_ret->pk_pasta);
				$objeto->setPasta($o_ret->pasta);
				$objeto->setUrl($o_ret->url);
				$objeto->setPastaStatus($o_ret->pastaStatus);
				$objeto->setIdPastaPai($o_ret->pk_pasta_pai);
				$objeto->setFilialCliente($o_ret->filialCliente_pk_filialCliente);
								
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
		$st_query = "SELECT pasta.*, filialCliente.* FROM pasta
						INNER JOIN filialCliente ON pasta.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente
						WHERE pasta.pk_pasta = ".$id.";";
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkPasta($o_ret->pk_pasta);
			$this->setPasta($o_ret->pasta);
			$this->setUrl($o_ret->url);
			$this->setPastaStatus($o_ret->pastaStatus);
			$this->setIdPastaPai($o_ret->pk_pasta_pai);
			$this->setFilialCliente($o_ret->filialCliente_pk_filialCliente);
			
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
	
		if (is_null($this->pkArquivo)) {
			$st_query = "INSERT INTO pasta
							(pk_pasta,
							pasta,
							url,
							pastaStatus,
							pk_pasta_pai,
							filialCliente_pk_filialCliente)
					   VALUES ('$this->pkPasta',
							'$this->pasta',
							'$this->url',
							'$this->pastaStatus',
							'$this->idPastaPai',
							'$this->idFilialCliente');";
		
		//echo $st_query; break;
		} else {
			$st_query = "UPDATE pasta 
						 SET	pasta = '$this->pasta',
							url = '$this->url',
							pastaStatus = '$this->pastaStatus',
							pk_pasta_pai = '$this->idPastaPai',
							filialCliente_pk_filialCliente = '$this->idFilialCliente'
					    WHERE pk_pasta = '$this->pkPasta'";
		
		//echo $st_query; break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkPasta)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_pasta')->fetchObject();
					return $o_ret->pk_pasta;
				} else {
					return $this->pkPasta;
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
		
		if (!is_null($this->pkArquivo)) {
			$st_query = "DELETE FROM pasta
						WHERE pk_pasta = $this->pkPasta";
			
			if ($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
