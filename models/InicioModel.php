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
 * Arquivo - InicioModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 
require_once 'models/UsuarioModel.php';
require_once 'models/FotoModel.php';
require_once 'models/ArquivoModel.php';

class InicioModel extends PersistModelAbstract {
 	// usuario
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
	
	// foto
	private $pkFoto;
	private $foto;
	private $caminhoFoto;
	private $tipoFoto;
	private $miniatura;
	
	// arquivos
	private $pkArquivo;
	private $descricaoArquivo;
	private $nomeArquivo;
	private $tipoArquivo;
	private $caminhoArquivo;
	
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters
	 */
	 
	 /***** ARQUIVOS *****/
	 
	 public function getPkArquivo() {
	   return $this->pkArquivo;
	 }
	 
	 public function setPkArquivo($pkArquivo) {
	   $this->pkArquivo = $pkArquivo;
	   return $this;
	 }
	 
	 public function getDescricaoArquivo() {
	   return $this->descricaoArquivo;
	 }
	 
	 public function setDescricaoArquivo($arquivo) {
	   $this->descricaoArquivo = $arquivo;
	   return $this;
	 }
	 
	 public function getNomeArquivo() {
	   return $this->nomeArquivo;
	 }
		   public function setNomeArquivo($nome) {
	   $this->nomeArquivo = $nome;
	   return $this;
	 }
	 
	 public function getTipoArquivo() {
	   return $this->tipoArquivo;
	 }
	 
	 public function setTipoArquivo($tipoArquivo) {
	   $this->tipoArquivo = $tipoArquivo;
	   return $this;
	 }
	 
	 public function getCaminhoArquivo() {
	   return $this->caminhoArquivo;
	 }
	 
	 public function setCaminhoArquivo($caminhoArquivo) {
	   $this->caminhoArquivo = $caminhoArquivo;
	   return $this;
	 }
	 
	 /***** FOTOS *****/
	 
	 public function getPkFoto() {
	   return $this->pkFoto;
	 }
		   public function setPkFoto($pkFoto) {
	   $this->pkFoto = $pkFoto;
	   return $this;
	 }
	 
	 public function getFoto() {
	   return $this->foto;
	 }
	 
	 public function setFoto($foto) {
	   $this->foto = $foto;
	   return $this;
	 }
	 
	 public function getCaminhoFoto() {
	   return $this->caminhoFoto;
	 }
	 
	 public function setCaminhoFoto($caminhoFoto) {
	   $this->caminhoFoto = $caminhoFoto;
	   return $this;
	 }
	 
	 public function getTipoFoto() {
	   return $this->tipoFoto;
	 }
	 
	 public function setTipoFoto($tipoFoto) {
	   $this->tipoFoto = $tipoFoto;
	   return $this;
	 }
	 
	 public function getMiniatura() {
	   return $this->miniatura;
	 }
	 
	 public function setMiniatura($miniatura) {
	   $this->miniatura = $miniatura;
	   return $this;
	 }
	 
	 /***** USUARIO *****/
	 
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
	 * do usuário, dos arquivos e das fotos
	*/
	public function _list($id = null) {
		
		if (!is_null($id)) {
			$st_query = "SELECT usuario.*, perfil.* FROM usuario 
						INNER JOIN perfil ON usuario.perfil_pk_perfil = perfil.pk_perfil
						WHERE usuario.pk_usuario LIKE '%$id%';";
		}
		
		//print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
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
			
			/**
			 * Busca os arquivos (relatorios) do cliente logado e 
			 * insere os registros encontrados no final do '$vetor'
			 */
			$st_query = "SELECT arquivo.*, filialCliente.*
							FROM arquivo
					    INNER JOIN filialCliente ON filialCliente.pk_filialCliente = arquivo.filialCliente_pk_filialCliente";
			
			if ($_SESSION['hashid'] == "cliente") {
				$st_query .= " AND filialCliente.cliente_pk_cliente = ".$_SESSION['idCliente']." 
					    		AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			} else			
			if ($_SESSION['hashid'] == "gerentefilial") {
				$st_query .= " AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			}// } else 
			// if ($_SESSION['hashid'] == "superadmin") {
				// $st_query .= " AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			// }
			
			$st_query .= " AND arquivo.arquivoStatus = 1 
						ORDER BY arquivo.pk_arquivo DESC LIMIT 5";
			
			try {
				$o_data = $this->o_db->query($st_query);
				
				while ($o_ret = $o_data->fetchObject()) {
					
					$arquivo = new ArquivoModel();
					
					$arquivo->setPkArquivo($o_ret->pk_arquivo);
					$arquivo->setDescricao($o_ret->descricao);
					$arquivo->setDtInclusao($o_ret->dataInclusao);
					$arquivo->setCaminho($o_ret->caminho);
					$arquivo->setDtVence($o_ret->dataVence);
					$arquivo->setNotificar($o_ret->notificar);
					$arquivo->setDtExclusao($o_ret->dataExclusao);
					$arquivo->setTipo($o_ret->tipo);
					$arquivo->setTamanho($o_ret->tamanho);
					$arquivo->setArquivoStatus($o_ret->arquivoStatus);
					$arquivo->setNomeArquivo($o_ret->nomeArquivo);
					$arquivo->setIdFilialCliente($o_ret->filialCliente_pk_filialCliente);
					
					array_push($vetor,$arquivo);
				}
				
			} catch (PDOException $e) { }
			
			/**
			 * Busca as fotos do cliente logado e 
			 * insere os registros encontrados no final do '$vetor'
			 */
			$st_query = "SELECT foto.*, filialCliente.*
						FROM foto
					    INNER JOIN filialCliente ON filialCliente.pk_filialCliente = foto.filialCliente_pk_filialCliente";
			
			if ($_SESSION['hashid'] == "cliente") {
				$st_query .= " AND filialCliente.cliente_pk_cliente = ".$_SESSION['idCliente']."
					    		AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			} else 
			if ($_SESSION['hashid'] == "gerentefilial") {
				$st_query .= " AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			} // } else 
			// if ($_SESSION['hashid'] == "superadmin") {
				// $st_query .= " AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			// }
			
			$st_query .= " AND foto.fotoStatus = 1 
						ORDER BY foto.pk_foto DESC LIMIT 6";
			
			//print $st_query; break;
			
			try {
				$o_data = $this->o_db->query($st_query);
				
				while ($o_ret = $o_data->fetchObject()) {
					
					$foto = new FotoModel();
					
					$foto->setPkFoto($o_ret->pk_foto);
					$foto->setLoja($o_ret->loja);
					$foto->setCoordenador($o_ret->coordenador);
					$foto->setPromotor($o_ret->promotor);
					$foto->setMesRefere($o_ret->mesReferencia);
					$foto->setDtInclusao($o_ret->dataInclusao);
					$foto->setDtVencimento($o_ret->dataVence);
					$foto->setDtExclusao($o_ret->dataExclusao);
					$foto->setFoto($o_ret->foto);
					$foto->setCaminhoFoto($o_ret->urlFoto);
					$foto->setTamanho($o_ret->tamanhoFoto);
					$foto->setTipo($o_ret->tipoFoto);
					$foto->setFotoStatus($o_ret->fotoStatus);
					$foto->setNotificar($o_ret->notificar);
					$foto->setIdFilialCliente($o_ret->filialCliente_pk_filialCliente);
					$foto->setMiniatura($o_ret->miniatura);
					
					array_push($vetor,$foto);
				}
				
			} catch (PDOException $e) { }
			
		}	
		catch (PDOException $e) { }

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
		try {
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