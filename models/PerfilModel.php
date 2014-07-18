<?php
// inicia a sessao
ob_start();	
session_start();
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - InicioController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class PerfilModel extends PersistModelAbstract {
 	
	private $pkPerfil;
	private $perfil;
	private $hashid;
	private $adicionar;
	private $editar;
	private $excluir;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	 
	 public function getPkPerfil() {
	 	return $this->pkPerfil;
	 }
	 
	 public function setPkPerfil($pk) {
	 	$this->pkPerfil = $pk;
		return $this;
	 }
	 
	 public function getPerfil() {
	 	return $this->perfil;
	 }
	 
	 public function setPerfil($perfil) {
	 	$this->perfil = $perfil;
		return $this;
	 }
	 
	 public function getHashid() {
	   return $this->hashid;
	 }
		   public function setHashid($hash) {
	   $this->hashid = $hash;
	   return $this;
	 }
	 
	 public function getAdicionar() {
	 	return $this->adicionar;
	 }
	 
	 public function setAdicionar($adicionar) {
	 	$this->adicionar = $adicionar;
		return $this;
	 }
	 
	 public function getEditar() {
	 	return $this->editar;
	 }
	 
	 public function setEditar($editar) {
	 	$this->editar = $editar;
		return $this;
	 }
	 
	 public function getExcluir() {
	 	return $this->excluir;
	 }
	 
	 public function setExcluir($excluir) {
	 	$this->excluir = $excluir;
		return $this;
	 }

	 /**
	* Retorna um array contendo os perfis
	*/
	public function _list($id = null) {
			
		if (!is_null($id))
			$st_query = "SELECT * FROM perfil WHERE pk_perfil LIKE '%$id%';";
		else
			$st_query = 'SELECT * FROM perfil;';
		
		$vetor = array();
		try {
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject()) {
				$objeto = new PerfilModel();
				$objeto->setPkPerfil($o_ret->pk_perfil);
				$objeto->setPerfil($o_ret->perfil);
				$objeto->setHashid($o_ret->hashid);
				$objeto->setAdicionar($o_ret->adicionar);
				$objeto->setEditar($o_ret->editar);
				$objeto->setExcluir($o_ret->excluir);

				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}
	
	/**
	* Retorna um array contendo os perfis
	* para ser utilizado nos campos selects
	*/
	public function _listSelector($id = null) {

		$st_query = 'SELECT * FROM perfil';
		
		if ($_SESSION['hashid'] == "gerentefilial") {
			$st_query .= " WHERE perfil.hashid LIKE 'cliente' OR perfil.hashid LIKE 'gerentefilial'";
		}
		
		if ($_SESSION['hashid'] == "superadmin") {
			$st_query .= " WHERE perfil.hashid LIKE 'cliente' OR perfil.hashid LIKE 'gerentefilial' OR perfil.hashid LIKE 'superadmin'";
		}
		
		//print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject()) {
				$objeto = new PerfilModel();
				$objeto->setPkPerfil($o_ret->pk_perfil);
				$objeto->setPerfil($o_ret->perfil);
				$objeto->setHashid($o_ret->hashid);
				$objeto->setAdicionar($o_ret->adicionar);
				$objeto->setEditar($o_ret->editar);
				$objeto->setExcluir($o_ret->excluir);

				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}
	
	/**
	* Retorna um array contendo os perfis
	* para ser utilizado nos filtros
	*/
	public function _filtroPerfis($id = null) {

		$st_query = 'SELECT * FROM perfil';
		
		if ($_SESSION['hashid'] == "superadmin") {
			$st_query .= " WHERE perfil.hashid LIKE 'cliente' OR perfil.hashid LIKE 'gerentefilial' OR perfil.hashid LIKE 'superadmin'";
		}
		
		//print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject()) {
				$objeto = new PerfilModel();
				$objeto->setPkPerfil($o_ret->pk_perfil);
				$objeto->setPerfil($o_ret->perfil);
				$objeto->setHashid($o_ret->hashid);
				$objeto->setAdicionar($o_ret->adicionar);
				$objeto->setEditar($o_ret->editar);
				$objeto->setExcluir($o_ret->excluir);

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
	public function loadById($id)
	{
		$vetor = array();
		$st_query = "SELECT * FROM perfil WHERE pk_perfil LIKE '".$id."';";
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			$this->setPkPerfil($o_ret->pk_perfil);
			$this->setPerfil($o_ret->perfil);
			$this->setHashid($o_ret->hashid);
			$this->setAdicionar($o_ret->adicionar);
			$this->setEditar($o_ret->editar);
			$this->setExcluir($o_ret->excluir);
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
	public function save()
	{
		if (is_null($this->pkPerfil)) {
			$st_query = "INSERT INTO perfil
							(pk_perfil,
							perfil,
							hashid,
							adicionar,
							editar,
							excluir)
					   VALUES ('$this->pkPerfil',
							'$this->perfil',
							'$this->hashid',
							'$this->adicionar',
							'$this->editar',
							'$this->excluir');";
		//echo $st_query; break;
		} else {
			$st_query = "UPDATE perfil 
						 SET	perfil = '$this->perfil',
							hashid = '$this->hashid',
							adicionar = '$this->adicionar',
							editar = '$this->editar',
							excluir = '$this->excluir'
					    WHERE pk_perfil = '$this->pkPerfil'";
		
		//echo $st_query; //break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkPerfil)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_perfil')->fetchObject();
					// 
					return $o_ret->pk_perfil;
				} else {
					return $this->pkPerfil;
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
	public function delete()
	{
		if (!is_null($this->pkPerfil))
		{
			$st_query = "DELETE FROM
							perfil
						WHERE pk_perfil = $this->pkPerfil";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
