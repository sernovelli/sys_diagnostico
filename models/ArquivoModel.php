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
 * Arquivo - ArquivoModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class ArquivoModel extends PersistModelAbstract {
 	
	private $pkArquivo;
	private $descricao;
	private $dtInclusao;
	private $caminho;
	private $dtVence;
	private $notificar;
	private $dtExclusao;
	private $tipo;
	private $tamanho;
	private $arquivoStatus;
	private $nomeArquivo;
	private $idFilialCliente;
	//private $nomePasta;
	private $idCliente;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	
	public function getPkArquivo() {
		return $this->pkArquivo;
	}
	
	public function setPkArquivo($pkArquivo) {
		$this->pkArquivo = $pkArquivo;
		return $this;
	}
	
	public function getDescricao() {
		return $this->descricao;
	}
	
	public function setDescricao($descricao) {
		$this->descricao = $descricao;
		return $this;
	}
	
	public function getDtInclusao() {
		return $this->dtInclusao;
	}
	
	public function setDtInclusao($dataInclusao) {
		$this->dtInclusao = $dataInclusao;
		return $this;
	}
	
	public function getCaminho() {
		return $this->caminho;
	}
	
	public function setCaminho($caminho) {
		$this->caminho = $caminho;
		return $this;
	}
	
	public function getDtVence() {
		return $this->dtVence;
	}
	
	public function setDtVence($dataVencimento) {
		$this->dtVence = $dataVencimento;
		return $this;
	}
	
	public function getNotificar() {
		return $this->notificar;
	}
	
	public function setNotificar($notificar) {
		$this->notificar = $notificar;
		return $this;
	}
	
	public function getDtExclusao() {
		return $this->dtExclusao;
	}
	
	public function setDtExclusao($dataExclusao) {
		$this->dtExclusao = $dataExclusao;
		return $this;
	}
	
	public function getTipo() {
		return $this->tipo;
	}
	
	public function setTipo($tipo) {
		$this->tipo = $tipo;
		return $this;
	}
	
	public function getTamanho() {
		return $this->tamanho;
	}
	
	public function setTamanho($tamanho) {
		$this->tamanho = $tamanho;
		return $this;
	}
	
	public function getArquivoStatus() {
		return $this->arquivoStatus;
	}
	
	public function setArquivoStatus($status) {
		$this->arquivoStatus = $status;
		return $this;
	}
	
	public function getNomeArquivo() {
	  return $this->nomeArquivo;
	}
	
	public function setNomeArquivo($arquivo) {
	  $this->nomeArquivo = $arquivo;
	  return $this;
	}
	
	public function getIdFilialCliente() {
	  return $this->idFilialCliente;
	}
	
	public function setIdFilialCliente($idFilialCliente) {
	  $this->idFilialCliente = $idFilialCliente;
	  return $this;
	}
	
	public function getIdCliente() {
	  return $this->idCliente;
	}
	
	public function setIdCliente($idCliente) {
	  $this->idCliente = $idCliente;
	  return $this;
	}
	
	/**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null) {
		
		// query para ações específicas do registro
		if (!is_null($id)) {
			 $st_query = "SELECT arquivo.* FROM arquivo
						WHERE arquivo.pk_arquivo = ".$id."
						  AND arquivo.arquivoStatus = 1 
					  ORDER BY arquivo.pk_arquivo ASC;";
		} else {
			
			// QUERY principal
			$st_query = "SELECT arquivo.*, filialCliente.*
						FROM arquivo
					    INNER JOIN filialCliente ON filialCliente.pk_filialCliente = arquivo.filialCliente_pk_filialCliente";
			
			if ($_SESSION['hashid'] == "cliente") {
				$st_query .= " AND filialCliente.cliente_pk_cliente = ".$_SESSION['idCliente']."
					    		AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
				
			} else 
			if ($_SESSION['hashid'] == "gerentefilial") {
				$st_query .= " AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			} else 
			if ($_SESSION['hashid'] == "superadmin") {
				//
			}
		}	
		
		$st_query .= " AND arquivo.arquivoStatus = 1 
					ORDER BY arquivo.pk_arquivo ASC;";
		
		//print $st_query; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new ArquivoModel();
				
				$objeto->setPkArquivo($o_ret->pk_arquivo);
				$objeto->setDescricao($o_ret->descricao);
				$objeto->setDtInclusao($o_ret->dataInclusao);
				$objeto->setCaminho($o_ret->caminho);
				$objeto->setDtVence($o_ret->dataVence);
				$objeto->setNotificar($o_ret->notificar);
				$objeto->setDtExclusao($o_ret->dataExclusao);
				$objeto->setTipo($o_ret->tipo);
				$objeto->setTamanho($o_ret->tamanho);
				$objeto->setArquivoStatus($o_ret->arquivoStatus);
				$objeto->setNomeArquivo($o_ret->nomeArquivo);
				$objeto->setIdFilialCliente($o_ret->filialCliente_pk_filialCliente);
				
				
				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}
	
	/**
	* Retorna um array contendo os registros
	*/
	public function _filtros($status = 1, $filial = 0, $cliente = 0) {
		$st_query = "SELECT arquivo.*, filialCliente.*, filial.*, cliente.* 
			    		FROM arquivo 
			    INNER JOIN filialCliente ON arquivo.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente 
			    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
			    INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente 
			    		 AND arquivo.arquivoStatus = ".$status;
		
		
		// FILTROS DO CLIENTE
		if ($_SESSION['hashid'] == "cliente") {
			$st_query .= " AND filialCliente.cliente_pk_cliente = ".$_SESSION['idCliente'];
		
			// por filial
			if ($filial != 0) {
				$st_query .= " AND filialCliente.filial_pk_filial = ".$filial;
			} else {
				$st_query .= " AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
			}
		
		} else 
		// FILTROS DO GERENTE DA FILIAL
		if ($_SESSION['hashid'] == "gerentefilial") {
			$st_query .= " AND filialCliente.filial_pk_filial = ".$_SESSION['idFilial'];
		
			// por cliente
			if ($cliente != 0) {
				$st_query .= " AND filialCliente.cliente_pk_cliente = ".$cliente;
			}
		
		} else 
		if ($_SESSION['hashid'] == "superadmin") {
			// por filial
			if ($filial != 0) {
				$st_query .= " AND filialCliente.filial_pk_filial = ".$filial;
			}
			
			// por cliente
			if ($cliente != 0) {
				$st_query .= " AND filialCliente.cliente_pk_cliente = ".$cliente;
			}
		}
		
		$st_query .= " ORDER BY arquivo.pk_arquivo ASC;";

		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new ArquivoModel();
				
				$objeto->setPkArquivo($o_ret->pk_arquivo);
				$objeto->setDescricao($o_ret->descricao);
				$objeto->setDtInclusao($o_ret->dataInclusao);
				$objeto->setCaminho($o_ret->caminho);
				$objeto->setDtVence($o_ret->dataVence);
				$objeto->setNotificar($o_ret->notificar);
				$objeto->setDtExclusao($o_ret->dataExclusao);
				$objeto->setTipo($o_ret->tipo);
				$objeto->setTamanho($o_ret->tamanho);
				$objeto->setArquivoStatus($o_ret->arquivoStatus);
				$objeto->setNomeArquivo($o_ret->nomeArquivo);
				$objeto->setIdFilialCliente($o_ret->filialCliente_pk_filialCliente);
				
				
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
		// $st_query = "SELECT arquivo.* FROM arquivo
				    // WHERE arquivo.pk_arquivo = ".$id.";";
		
		$st_query = "SELECT arquivo.*, filialCliente.*, cliente.*
					FROM arquivo
			    INNER JOIN filialCliente ON filialCliente.pk_filialCliente = arquivo.filialCliente_pk_filialCliente
			    INNER JOIN cliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
				    WHERE arquivo.pk_arquivo = ".$id;
					    
		//print $st_query; break;
		
		try {
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			
			$this->setPkArquivo($o_ret->pk_arquivo);
			$this->setDescricao($o_ret->descricao);
			$this->setDtInclusao($o_ret->dataInclusao);
			$this->setCaminho($o_ret->caminho);
			$this->setDtVence($o_ret->dataVence);
			$this->setNotificar($o_ret->notificar);
			$this->setDtExclusao($o_ret->dataExclusao);
			$this->setTipo($o_ret->tipo);
			$this->setTamanho($o_ret->tamanho);
			$this->setArquivoStatus($o_ret->arquivoStatus);
			$this->setNomeArquivo($o_ret->nomeArquivo);
			$this->setIdFilialCliente($o_ret->filialCliente_pk_filialCliente);
			$this->setIdCliente($o_ret->pk_cliente);
			
			return $this;
		}
		catch(PDOException $e)
		{}
		return false;
	}

	
	/**
	* Salva dados contidos na instancia da classe na tabela de perfil. Se o ID for passado,
	* um UPDATE sera executado, caso contr�rio, um INSERT sera executado
	*/
	public function save() {
	
		if (is_null($this->pkArquivo)) {
			$st_query = "INSERT INTO arquivo
							(pk_arquivo,
							descricao,
							dataInclusao,
							dataVence,
							dataExclusao,
							notificar,
							arquivoStatus,
							nomeArquivo,
							tipo,
							tamanho,
							caminho,
							filialCliente_pk_filialCliente)
					   VALUES ('$this->pkArquivo',
							'$this->descricao',
							'$this->dtInclusao',
							'$this->dtVence',
							'$this->dtExclusao',
							'$this->notificar',
							'$this->arquivoStatus',
							'$this->nomeArquivo',
							'$this->tipo',
							'$this->tamanho',
							'$this->caminho',
							'$this->idFilialCliente');";
		
		//print "<br />".$st_query; break;
		} else {
			$st_query = "UPDATE arquivo 
						 SET	descricao = '$this->contrato',
							dataInclusao = '$this->dtInclusao',
							dataVence = '$this->dtVence',
							dataExclusao = '$this->dtExclusao',
							notificar = '$this->notificar',
							arquivoStatus = '$this->arquivoStatus',
							nomeArquivo = '$this->nomeArquivo',
							tipo = '$this->tipo',
							tamanho = '$this->tamanho',
							caminho = '$this->caminho',
							filialCliente_pk_filialCliente = '$this->idFilialCliente'
					    WHERE pk_arquivo = '$this->pkArquivo'";
		
		//echo $st_query; break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkArquivo)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_arquivo')->fetchObject();
					return $o_ret->pk_arquivo;
				} else {
					return $this->pkArquivo;
				}
		}
		catch (PDOException $e) {
			throw $e;
		}
		return false;
	}
	
	/**
	 * Altera o campo 'notificarArquivos' na tabela cliente
	 * para que o sistema envie o e-mail de notificação de novas publicações.
	 */
	public function ativaNotificarArquivos($idCliente) {

		$st_query = "UPDATE cliente 
					 SET	notificarArquivos = '1'
				    WHERE pk_cliente = ".$idCliente;

		// print $st_query; break;

		try {
			if ($this -> o_db -> exec($st_query) > 0)
				return true;
				
		} catch (PDOException $e) {
			throw $e->getMessage();
		}
		return false;
	}
	
	/**
	 * Publica/despublica um arquivo conforme
	 * id da classe.
	 */
	public function publicar() {

		$status = ($this->arquivoStatus == 1) ? 2 : 1;
		
		$st_query = "UPDATE arquivo 
					 SET	arquivoStatus = '$status'
				    WHERE pk_arquivo = '$this->pkArquivo'";
		
		// print $st_query; break;
		
		try {
			if ($this->o_db->exec($st_query) > 0)
				return true;
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
			$st_query = "DELETE FROM arquivo
					    WHERE pk_arquivo = $this->pkArquivo";
			
			if ($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}
 }
 
 ?>
