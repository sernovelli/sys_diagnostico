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
 * Arquivo - UsuarioClienteModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class UsuarioClienteModel extends PersistModelAbstract {
 	
	private $pkClienteUsuario;
	
	// filial
	private $pkdoCliente;
	private $nomedoCliente;
	
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
	
	public function getPkClienteUsuario() {
		return $this->pkClienteUsuario;
	}
	
	public function setPkClienteUsuario($idClienteUsuario) {
		$this->pkClienteUsuario = $idClienteUsuario;
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
	public function _list($id = null, $usuario = "", $cliente = "") {
			
		if (!is_null($id)) {
			$st_query = "SELECT cliente.*, usuario.*, clienteUsuario.* FROM cliente
						INNER JOIN clienteUsuario ON clienteUsuario.cliente_pk_cliente = cliente.pk_cliente
						INNER JOIN usuario ON usuario.pk_usuario = clienteUsuario.usuario_pk_usuario
						WHERE clienteUsuario.pk_clienteUsuario = ".$id." 
						ORDER BY cliente.pk_cliente ASC;";
		} else {
				
			$st_query = "SELECT clienteUsuario.*, cliente.*, usuario.*
						FROM clienteUsuario
				    INNER JOIN cliente ON cliente.pk_cliente = clienteUsuario.cliente_pk_cliente
				    INNER JOIN usuario ON usuario.pk_usuario = clienteUsuario.usuario_pk_usuario";
			
			if ($_SESSION['hashid'] == "superadmin") {
				$st_query = "SELECT clienteUsuario.*, cliente.*, usuario.*, perfil.*, filialCliente.*, filial.*
							FROM clienteUsuario
					    INNER JOIN cliente ON cliente.pk_cliente = clienteUsuario.cliente_pk_cliente
					    INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
					    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial
					    INNER JOIN usuario ON usuario.pk_usuario = clienteUsuario.usuario_pk_usuario
					    INNER JOIN perfil ON perfil.pk_perfil = usuario.perfil_pk_perfil
							 AND perfil.hashid LIKE 'cliente'";
			
				// FILTROS
				// filtro por usuario
				if ($usuario != "") {
					$st_query .= " AND usuario.nome LIKE '%".$usuario."%'";
				}
				
				// filtro por cliente
				if ($cliente != "") {
					$st_query .= " AND cliente.fantasia LIKE '%".$cliente."%'";
				}
			
			}
			
			if ($_SESSION['hashid'] == "gerentefilial") {
				$st_query = "SELECT clienteUsuario.*, cliente.*, usuario.*, perfil.*, filialCliente.*, filial.*
							FROM clienteUsuario
					    INNER JOIN cliente ON cliente.pk_cliente = clienteUsuario.cliente_pk_cliente
					    INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
					    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
					    INNER JOIN usuario ON usuario.pk_usuario = clienteUsuario.usuario_pk_usuario
					    INNER JOIN perfil ON perfil.pk_perfil = usuario.perfil_pk_perfil
							 AND perfil.hashid LIKE 'cliente'
					    		 AND filial.pk_filial = ".$_SESSION['idFilial'];
			}
			
			$st_query .= " ORDER BY cliente.pk_cliente ASC;";
		}
		
		// print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new UsuarioClienteModel();
				
				$objeto->setPkClienteUsuario($o_ret->pk_clienteUsuario);
				$objeto->setPkdoCliente($o_ret->pk_cliente);
				$objeto->setNomedoCliente($o_ret->fantasia);
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
		$st_query = "SELECT cliente.*, usuario.*, clienteUsuario.* 
					FROM cliente 
					INNER JOIN clienteUsuario ON clienteUsuario.cliente_pk_cliente = cliente.pk_cliente 
					INNER JOIN usuario ON usuario.pk_usuario = clienteUsuario.usuario_pk_usuario 
					WHERE clienteUsuario.pk_clienteUsuario =".$id.";";
		
		//print $st_query; //break;
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkClienteUsuario($o_ret->pk_clienteUsuario);
			$this->setPkdoCliente($o_ret->pk_cliente);
			$this->setNomedoCliente($o_ret->fantasia);
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
	
		if (is_null($this->getPkClienteUsuario())) {
			// verifica se já existe um registro com os dados repassados
			$st_query = "SELECT * FROM clienteUsuario 
						WHERE cliente_pk_cliente = ".$this->getPkdoCliente()."
						AND usuario_pk_usuario = ".$this->getPkdoUsuario();
			
			$o_data = $this->o_db->query($st_query);
			//$o_ret = $o_data->fetchObject();
			
			if ($o_data->fetchObject() > 0) {
				return -1;
			}
			
			$st_query = "INSERT INTO clienteUsuario
							(pk_clienteUsuario,
							cliente_pk_cliente,
							usuario_pk_usuario)
					   VALUES ('$this->pkClienteUsuario',
							'$this->pkdoCliente',
							'$this->pkdoUsuario');";
		
		//echo $st_query; break;
		} else {
			//print $this->pkFilialUsuario; break;
			
			$st_query = "UPDATE clienteUsuario 
						 SET	usuario_pk_usuario = '$this->pkdoUsuario',
							cliente_pk_cliente = '$this->pkdoCliente'
					    WHERE pk_clienteUsuario = '$this->pkClienteUsuario'";
		
		//echo $st_query; //break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkClienteUsuario)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_clienteUsuario')->fetchObject();
					return $o_ret->pk_clienteUsuario;
				} else {
					return $this->pkClienteUsuario;
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
		
		if (!is_null($this->pkClienteUsuario)) {
			$st_query = "DELETE FROM clienteUsuario
						WHERE pk_clienteUsuario = $this->pkClienteUsuario";
			
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
