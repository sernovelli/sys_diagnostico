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
 * Arquivo - UsuarioModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class UsuarioModel extends PersistModelAbstract {
 	
	private $pkUsuario;
	private $nome;
	private $cargo;
	private $telefone;
	private $email;
	private $login;
	private $senha;
	private $cpf;
	private $usuarioStatus;
	private $idPerfil;
	private $nomePerfil;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	 
	 public function getPkUsuario() {
	 	return $this->pkUsuario;
	 }
	 
	 public function setPkUsuario($pkUsuario) {
	 	$this->pkUsuario = $pkUsuario;
		return $this;
	 }
	 
	 public function getNome() {
	 	return $this->nome;
	 }
	 
	 public function setNome($nome) {
	 	$this->nome = $nome;
		return $this;
	 }
	 
	 public function getCargo() {
	 	return $this->cargo;
	 }
	 
	 public function setCargo($cargo) {
	 	$this->cargo = $cargo;
		return $this;
	 }
	 
	 public function getTelefone() {
	 	return $this->telefone;
	 }
	 
	 public function setTelefone($telefone) {
	 	$this->telefone = $telefone;
		return $this;
	 }
	 
	 public function getEmail() {
	 	return $this->email;
	 }
	 
	 public function setEmail($email) {
	 	$this->email = $email;
		return $this;
	 }
	 
	 public function getLogin() {
	 	return $this->login;
	 }
	 
	 public function setLogin($login) {
	 	$this->login = $login;
		return $this;
	 }
	 
	 public function getSenha() {
	 	return $this->senha;
	 }
	 
	 public function setSenha($senha) {
	 	$this->senha = $senha;
		return $this;
	 }
	 
	 public function getCpf() {
	 	return $this->cpf;
	 }
	 
	 public function setCpf($cpf) {
	 	$this->cpf = $cpf;
		return $this;
	 }
	 
	 public function getUsuarioStatus() {
	 	return $this->usuarioStatus;
	 }
	 
	 public function setUsuarioStatus($usuarioStatus) {
	 	$this->usuarioStatus = $usuarioStatus;
		return $this;
	 }
	 
	 public function getIdPerfil() {
	 	return $this->idPerfil;
	 }
	 
	 public function setIdPerfil($pkPerfil) {
	 	$this->idPerfil = $pkPerfil;
		return $this;
	 }
	 
	 public function getNomePerfil() {
	 	return $this->nomePerfil;
	 }
	 
	 public function setNomePerfil($nomePerfil) {
	 	$this->nomePerfil = $nomePerfil;
		return $this;
	 }


	 /**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null, $nome = "", $perfil = 0, $status = 0) {
			
		if (!is_null($id)) {
			$st_query = "SELECT usuario.*, perfil.* FROM usuario 
						INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
						WHERE usuario.pk_usuario LIKE '%$id%';";
		} else {
			$st_query = "SELECT usuario.*, perfil.*
						FROM usuario 
				    INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil";
				    
			if ($_SESSION['hashid'] == "gerentefilial") {
				$st_query = "SELECT usuario.*, perfil.*, filialCliente.*, filial.*, clienteUsuario.*, cliente.* 
							 FROM usuario 
					     INNER JOIN clienteUsuario ON clienteUsuario.usuario_pk_usuario = usuario.pk_usuario
						INNER JOIN cliente ON cliente.pk_cliente = clienteUsuario.cliente_pk_cliente
					     INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
					     INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
					     INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial AND filial.pk_filial = ".$_SESSION['idFilial']."
							  AND perfil.hashid = 'cliente' ";
			
				//FILTROS
				// filtro por nome
				if ($nome != "") {
					$st_query .= " AND usuario.nome LIKE '%".$nome."'";
				}
			
			}
			
			if ($_SESSION['hashid'] == "superadmin") {
				//$st_query .= " AND perfil.hashid != 'desenvolvedor'";
				
				// FILTROS
				// filtro por nome
				if ($nome != "") {
					$st_query .= " AND usuario.nome LIKE '%".$nome."'";
				}
				
				// filtro por perfil
				if ($perfil != "") {
					$st_query .= " AND perfil.hashid LIKE '".$perfil."'";
				} else {
					$st_query .= " AND perfil.hashid != 'desenvolvedor'";
				}
				
				// filtro por status
				if ($status != 0) {
					$st_query .= " AND usuario.usuarioStatus = ".$status;
				}
			}
			
			$st_query .= " GROUP BY usuario.pk_usuario
				    	 ORDER BY usuario.pk_usuario ASC";
		}				
		
		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		try {
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject()) {
					
				$objeto = new UsuarioModel();
				
				$objeto->setPkUsuario($o_ret->pk_usuario);
				$objeto->setNome($o_ret->nome);
				$objeto->setCargo($o_ret->cargo);
				$objeto->setTelefone($o_ret->telefone);
				$objeto->setEmail($o_ret->email);
				$objeto->setLogin($o_ret->login);
				$objeto->setSenha($o_ret->senha);
				$objeto->setCpf($o_ret->cpf);
				$objeto->setUsuarioStatus($o_ret->usuarioStatus);
				$objeto->setIdPerfil($o_ret->perfil_pk_perfil);
				$objeto->setNomePerfil($o_ret->perfil);

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
		$st_query = "SELECT usuario.*, perfil.* FROM usuario 
					INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
					WHERE usuario.pk_usuario LIKE '".$id."';";
		
		//print $st_query; break;
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			$this->setPkUsuario($o_ret->pk_usuario);
			$this->setNome($o_ret->nome);
			$this->setCargo($o_ret->cargo);
			$this->setTelefone($o_ret->telefone);
			$this->setEmail($o_ret->email);
			$this->setLogin($o_ret->login);
			$this->setSenha($o_ret->senha);
			$this->setCpf($o_ret->cpf);
			$this->setUsuarioStatus($o_ret->usuarioStatus);
			$this->setIdPerfil($o_ret->perfil_pk_perfil);
			$this->setNomePerfil($o_ret->perfil);
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
		
		if (is_null($this->pkUsuario)) {
			$st_query = "INSERT INTO usuario
							(pk_usuario,
							nome,
							cargo,
							telefone,
							email,
							login,
							senha,
							cpf,
							usuarioStatus,
							perfil_pk_perfil)
					   VALUES ('$this->pkUsuario',
							'$this->nome',
							'$this->cargo',
							'$this->telefone',
							'$this->email',
							'$this->login',
							'$this->senha',
							'$this->cpf',
							'$this->usuarioStatus',
							'$this->idPerfil');";
		//echo $st_query; break;
		} else {
			$st_query = "UPDATE usuario 
						 SET	nome = '$this->nome',
							cargo = '$this->cargo',
							telefone = '$this->telefone',
							email = '$this->email',
							login = '$this->login',
							senha = '$this->senha',
							cpf = '$this->cpf',
							usuarioStatus = '$this->usuarioStatus',
							perfil_pk_perfil = '$this->idPerfil'
					    WHERE pk_usuario = '$this->pkUsuario'";
		
		//echo $st_query; //break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkUsuario)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_usuario')->fetchObject();
					return $o_ret->pk_usuario;
				} else {
					return $this->pkUsuario;
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
		
		if (!is_null($this->pkUsuario)) {
			$st_query = "DELETE FROM
							usuario
						WHERE pk_usuario = $this->pkUsuario";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
	
	 /**
	 * Retorna os dados de um gerente-filial conforme o id da filial
	 * para o formulário de suporte.
	 */
	public function loadByIdFilial($idFilial) {
		
		$vetor = array();
		$st_query = "SELECT usuario.*, perfil.*, filialUsuario.* FROM usuario 
				    INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
				    INNER JOIN filialUsuario ON filialUsuario.filial_pk_filial = '".$idFilial."'
				    WHERE filialUsuario.usuario_pk_usuario = usuario.pk_usuario
				      AND usuario.usuarioStatus = 1 
				      AND perfil.hashid LIKE 'gerentefilial'";
		
		// print $st_query; break;
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkUsuario($o_ret->pk_usuario);
			$this->setNome($o_ret->nome);
			$this->setCargo($o_ret->cargo);
			$this->setTelefone($o_ret->telefone);
			$this->setEmail($o_ret->email);
			$this->setLogin($o_ret->login);
			$this->setSenha($o_ret->senha);
			$this->setCpf($o_ret->cpf);
			$this->setUsuarioStatus($o_ret->usuarioStatus);
			$this->setIdPerfil($o_ret->perfil_pk_perfil);
			$this->setNomePerfil($o_ret->perfil);
			
			return $this;
		}
		catch(PDOException $e)
		{
			//throw $e; 
		}
		return false;

	}
	
	/**
	 * Traz os dados do super administrador para o formulário de suporte
	 * quando um gerente de filial está logado e acessa o suporte.
	 */
	 public function loadSuperAdmin() {
	 	$vetor = array();
		$st_query = "SELECT usuario.*, perfil.* 
					FROM usuario 
			    INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
				      AND usuario.usuarioStatus = 1 
				      AND perfil.hashid LIKE 'superadmin';";
		
		//print $st_query;
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			$this->setPkUsuario($o_ret->pk_usuario);
			$this->setNome($o_ret->nome);
			$this->setCargo($o_ret->cargo);
			$this->setTelefone($o_ret->telefone);
			$this->setEmail($o_ret->email);
			$this->setLogin($o_ret->login);
			$this->setSenha($o_ret->senha);
			$this->setCpf($o_ret->cpf);
			$this->setUsuarioStatus($o_ret->usuarioStatus);
			$this->setIdPerfil($o_ret->perfil_pk_perfil);
			$this->setNomePerfil($o_ret->perfil);
			return $this;
		}
		catch(PDOException $e)
		{}
		return false;
	 }
 }
 
 ?>
