<?php

/**
 * @author Sergio Novelli
 * @version 1.0
 *
 * Camada - Modelo ou Model.
 * Diretorio Pai - models
 * Arquivo - TelefoneModel
 *
 * Responsavel por gerenciar a autenticacao do usuario
 **/
class AutenticarModel extends PersistModelAbstract {
	//usuario
	private $pk_usuario;
	private $nome;
	private $login;
	private $senha;
	private $usuarioStatus;
	private $email;
	private $telefone;
	// perfil
	private $perfil_pk_perfil;
	// filial
	private $filial;
	private $idFilial;
	//perfil
	private $nomePerfil;
	private $hashid;
	private $adicionar;
	private $editar;
	private $excluir;
	//cliente
	private $pkCliente;

	function __construct() {
		parent::__construct();
	}

	/**
	 * Setters e Getters da
	 * classe AutenticarModel
	 */

	public function setPk($pk_usuario) {
		$this->pk_usuario = $pk_usuario;
		return $this;
	}
	
	public function getPk() {
		return $this->pk_usuario;
	}

	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}
	
	public function getNome() {
		return $this->nome;
	}

	public function setLogin($login) {
		$this->login = $login;
		return $this;
	}

	public function getLogin() {
		return $this->login;
	}
	
	public function setSenha($senha) {
		$this->senha = $senha;
		return $this;
	}
	
	public function getSenha() {
		return $this->senha;
	}
	
	public function setUsuarioStatus($usuarioStatus) {
		$this->usuarioStatus = $usuarioStatus;
		return $this;
	}
	
	public function getUsuarioStatus() {
		return $this->usuarioStatus;
	}
	
	public function getEmail() {
	  return $this->email;
	}
	
	public function setEmail($email) {
	  $this->email = $email;
	  return $this;
	}
	
	public function getTelefone() {
	  return $this->telefone;
	}
	
	public function setTelefone($tel) {
	  $this->telefone = $tel;
	  return $this;
	}
	
	// perfil
	public function setPerfil($perfil) {
		$this->perfil_pk_perfil = $perfil;
		return $this;
	}
	
	public function getPerfil() {
		return $this->perfil_pk_perfil;
	}
	
	// Filial
	public function getIdFilial() {
		return $this->idFilial;
	}
	
	public function setIdFilial($idFilial) {
		$this->idFilial = $idFilial;
		return $this;
	}
	
	public function setFilial($filial) {
		$this->filial = $filial;
		return $this;
	}
	
	public function getFilial() {
		return $this->filial;
	}
	
	// Perfil
	public function getNomePerfil() {
		return $this->nomePerfil;
	}
	
	public function setNomePerfil($nomePerfil) {
		$this->nomePerfil = $nomePerfil;
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
	 
	 public function getPkCliente() {
	   return $this->pkCliente;
	 }
		   public function setPkCliente($idCliente) {
	   $this->pkCliente = $idCliente;
	   return $this;
	 }
	 

	/**
	 * Retorna os dados do usuario se este for usuario da filial Dinamica
	 */
	public function loadByUsuarioFilial($usuario) {
		$v_usuarios = array();
		$st_query = "SELECT usuario.*, filialUsuario.*, filial.*, perfil.*
				   FROM usuario 
				   INNER JOIN filialUsuario ON filialUsuario.usuario_pk_usuario = usuario.pk_usuario
				   INNER JOIN filial ON filialUsuario.filial_pk_filial = filial.pk_filial
				   INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
				   WHERE usuario.login LIKE '".$usuario."';";
		
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		
		//usuario
		$this->setPk($o_ret->pk_usuario);
		$this->setNome($o_ret->nome);
		$this->setLogin($o_ret->login);
		$this->setSenha($o_ret->senha);
		$this->setUsuarioStatus($o_ret->usuarioStatus);
		$this->setEmail($o_ret->email);
		$this->setTelefone($o_ret->telefone);
		// perfil
		$this->setPerfil($o_ret->perfil_pk_perfil);
		// filial
		$this->setFilial($o_ret->filial);
		$this->setIdFilial($o_ret->pk_filial);
		
		//perfil
		$this->setNomePerfil($o_ret->perfil);
		$this->setHashid($o_ret->hashid);
		$this->setAdicionar($o_ret->adicionar);
		$this->setEditar($o_ret->editar);
		$this->setExcluir($o_ret->excluir);
		return $this;
	}
	
	/**
	 * Retorna os dados do usuario se este for usuario do cliente
	 */
	public function loadByUsuarioCliente($usuario) {
		
		$st_query = "SELECT usuario.*, clienteUsuario.*, cliente.*, perfil.*
				     FROM usuario 
				    INNER JOIN clienteUsuario ON clienteUsuario.usuario_pk_usuario = usuario.pk_usuario
				    INNER JOIN cliente ON clienteUsuario.cliente_pk_cliente = cliente.pk_cliente
				    INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
				    WHERE usuario.login LIKE '".$usuario."';";

		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		// usuario
		$this->setPk($o_ret->pk_usuario);
		$this->setNome($o_ret->nome);
		$this->setLogin($o_ret->login);
		$this->setSenha($o_ret->senha);
		$this->setUsuarioStatus($o_ret->usuarioStatus);
		$this->setEmail($o_ret->email);
		$this->setTelefone($o_ret->telefone);
		// perfil
		$this->setPerfil($o_ret->perfil_pk_perfil);
		$this->setNomePerfil($o_ret->perfil);
		$this->setHashid($o_ret->hashid);
		$this->setAdicionar($o_ret->adicionar);
		$this->setEditar($o_ret->editar);
		$this->setExcluir($o_ret->excluir);
		// cliente
		$this->setPkCliente($o_ret->pk_cliente);
		
		$st_query = "SELECT cliente.*, filialCliente.*, filial.*
				     FROM cliente 
			    	    INNER JOIN filialCliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
			         INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial
				    WHERE cliente.pk_cliente = '".$this->getPkCliente()."';";
		
		//print $st_query; break;
		
		$o_data = $this->o_db->query($st_query);
		$o_ret = $o_data->fetchObject();
		// filial
		$this->setFilial($o_ret->filial);
		$this->setIdFilial($o_ret->pk_filial);
		
		return $this;
	}
}
?>