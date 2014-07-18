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
 * Arquivo - UsuarioFilialModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class UsuarioFilialModel extends PersistModelAbstract {
 	
	private $pkFilialUsuario;
	
	// filial
	private $pkdaFilial;
	private $nomedafilial;
	
	// usuario
	private $pkdoUsuario;
	private $nomedoUsuario;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	
	public function getPkFilialUsuario() {
		return $this->pkFilialUsuario;
	}
	
	public function setPkFilialUsuario($idFilialUsuario) {
		$this->pkFilialUsuario = $idFilialUsuario;
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
	 	return $this->filial;
	}
	 
	public function setNomedaFilial($filial) {
	 	$this->filial = $filial;
		return $this;
	}
	 
	public function getPkdoUsuario() {
		return $this->pkdoUsuario;
	}
	
	public function setPkdoUsuario($idUsuario) {
		$this->pkdoUsuario = $idUsuario;
		return $this;
	}
	
	public function getNomedoUsuario() {
		return $this->nomeUsuario;
	}
	
	public function setNomedoUsuario($usuario) {
		$this->nomeUsuario = $usuario;
		return $this;
	}
	

	 /**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null, $nome = "", $filial = 0) {
			
		if (!is_null($id)) {
			$st_query = "SELECT filial.*, usuario.*, filialUsuario.* FROM filial
						INNER JOIN filialUsuario ON filialUsuario.filial_pk_filial = filial.pk_filial
						INNER JOIN usuario ON usuario.pk_usuario = filialUsuario.usuario_pk_usuario
						WHERE filial.pk_filial = ".$id." 
						ORDER BY filial.pk_filial ASC;";
		} else {
				
			$st_query = "SELECT filial.*, usuario.*, filialUsuario.*, perfil.*
							FROM filial 
					    INNER JOIN filialUsuario ON filialUsuario.filial_pk_filial = filial.pk_filial
					    INNER JOIN usuario ON usuario.pk_usuario = filialUsuario.usuario_pk_usuario
					    INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil";
							 
			if ($_SESSION['hashid'] == "superadmin") {
				$st_query .= " AND perfil.hashid NOT LIKE 'desenvolvedor'";
				// FILTROS
				// filtro por nome
				if ($nome != "") {
					$st_query .= " AND usuario.nome LIKE '%".$nome."%'";
				}
				
				// filtro por filial
				if ($filial != 0) {
					$st_query .= " AND filial.pk_filial = ".$filial;
				}
			}
				
			if ($_SESSION['hashid'] == "gerentefilial") {
				$st_query = "SELECT filial.*, usuario.*, filialUsuario.*, perfil.*
							FROM filial 
					    INNER JOIN filialUsuario ON filialUsuario.filial_pk_filial = filial.pk_filial
					    		 AND filialUsuario.filial_pk_filial = ".$_SESSION['idFilial']."
					    INNER JOIN usuario ON usuario.pk_usuario = filialUsuario.usuario_pk_usuario
					    INNER JOIN perfil ON perfil.pk_perfil = usuario.perfil_pk_perfil 
					    		 AND perfil.hashid = 'gerentefilial'";
			}
			
			$st_query .= " WHERE usuario.usuarioStatus = '1'
					  ORDER BY filial.pk_filial ASC";
		}
		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new UsuarioFilialModel();
				
				$objeto->setPkFilialUsuario($o_ret->pk_filialUsuario);
				$objeto->setPkdaFilial($o_ret->pk_filial);
				$objeto->setNomedaFilial($o_ret->filial);
				$objeto->setPkdoUsuario($o_ret->usuario_pk_usuario);
				$objeto->setNomedoUsuario($o_ret->nome);
				
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
		//print $id; break;
		$vetor = array();
		$st_query = "SELECT filial.*, usuario.*, filialUsuario.* FROM filialUsuario 
					INNER JOIN filial ON filialUsuario.filial_pk_filial = filial.pk_filial 
					INNER JOIN usuario ON usuario.pk_usuario = filialUsuario.usuario_pk_usuario 
					WHERE filialUsuario.pk_filialUsuario = ".$id.";";
		
		//print $st_query; //break;
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkFilialUsuario($o_ret->pk_filialUsuario);
			$this->setPkdaFilial($o_ret->pk_filial);
			$this->setNomedaFilial($o_ret->filial);
			$this->setPkdoUsuario($o_ret->usuario_pk_usuario);
			$this->setNomedoUsuario($o_ret->nome);
			
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
	
		if (is_null($this->getPkFilialUsuario())) {
			// verifica se já existe um registro com os dados repassados
			$st_query = "SELECT * FROM filialUsuario 
						WHERE filial_pk_filial = ".$this->getPkdaFilial()."
						AND usuario_pk_usuario = ".$this->getPkdoUsuario();
			
			$o_data = $this->o_db->query($st_query);
			//$o_ret = $o_data->fetchObject();
			
			if ($o_data->fetchObject() > 0) {
				return -1;
			}
			
			$st_query = "INSERT INTO filialUsuario
							(pk_filialUsuario,
							usuario_pk_usuario,
							filial_pk_filial)
					   VALUES ('$this->pkFilialUsuario',
							'$this->pkdoUsuario',
							'$this->pkdaFilial');";
		
		//echo $st_query; break;
		} else {
			//print $this->pkFilialUsuario; break;
			
			$st_query = "UPDATE filialUsuario 
						 SET	usuario_pk_usuario = '$this->pkdoUsuario',
							filial_pk_filial = '$this->pkdaFilial'
					    WHERE pk_filialUsuario = '$this->pkFilialUsuario'";
		
		//echo $st_query; //break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkFilialUsuario)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_filialUsuario')->fetchObject();
					return $o_ret->pk_filialUsuario;
				} else {
					return $this->pkFilialUsuario;
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
		
		if (!is_null($this->pkFilialUsuario)) {
			$st_query = "DELETE FROM filialUsuario
						WHERE pk_filialUsuario = $this->pkFilialUsuario";
			
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
