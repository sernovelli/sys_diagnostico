<?php

require_once "models/ArquivoModel.php";

/**
 * Classe para despublicar fotos publicadas a 90 ou mais dias
 */
 
 class ExcluiArquivosModel extends PersistModelAbstract {
 	
	private $pkArquivo;
	//private $descricao;
	//private $dtInclusao;
	private $caminho;
	private $dtVence;
	//private $notificar;
	//private $dtExclusao;
	private $tipo;
	//private $tamanho;
	private $arquivoStatus;
	private $nomeArquivo;
	private $idFilialCliente;
	//private $nomePasta;
	//private $idCliente;
	
	// filialCliente
	private $pkDoCliente;
	private $pkDaFilial;
	
	// cliente
	private $fantasia;
	private $contrato;
	
	// filial
	private $pkFilial;
	private $filial;
	
	//private $hoje = Date("Y-m-d");
	private $hoje = "2014-02-24"; // testes
	

	
	function __construct() {
		parent::__construct();
		$this->hoje = date('Y-m-d', strtotime($this->hoje));
	}
	
	public function getPkArquivo() {
		return $this->pkArquivo;
	}
	
	public function setPkArquivo($pkArquivo) {
		$this->pkArquivo = $pkArquivo;
		return $this;
	}
	
	// public function getDescricao() {
		// return $this->descricao;
	// }
// 	
	// public function setDescricao($descricao) {
		// $this->descricao = $descricao;
		// return $this;
	// }
	
	// public function getDtInclusao() {
		// return $this->dtInclusao;
	// }
// 	
	// public function setDtInclusao($dataInclusao) {
		// $this->dtInclusao = $dataInclusao;
		// return $this;
	// }
	
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
	
	// public function getNotificar() {
		// return $this->notificar;
	// }
// 	
	// public function setNotificar($notificar) {
		// $this->notificar = $notificar;
		// return $this;
	// }
	
	// public function getDtExclusao() {
		// return $this->dtExclusao;
	// }
// 	
	// public function setDtExclusao($dataExclusao) {
		// $this->dtExclusao = $dataExclusao;
		// return $this;
	// }
	
	public function getTipo() {
		return $this->tipo;
	}
	
	public function setTipo($tipo) {
		$this->tipo = $tipo;
		return $this;
	}
	
	// public function getTamanho() {
		// return $this->tamanho;
	// }
// 	
	// public function setTamanho($tamanho) {
		// $this->tamanho = $tamanho;
		// return $this;
	// }
	
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
	
	
	// cliente
	public function setContrato($nContrato) {
		$this->contrato = $nContrato;
		return $this;
	}
	
	public function getContrato() {
		return $this->contrato;
	}
	
	public function setNomeFantasia($nFantasia) {
		$this->nomeFantasia = $nFantasia;
		return $this;
	}
	
	public function getNomeFantasia() {
		return $this->nomeFantasia;
	}
	
	
	// filial
	public function getFilial() {
	 	return $this->filial;
	}
	 
	public function setFilial($filial) {
	 	$this->filial = $filial;
		return $this;
	}
	
	
	// FilialCliente
	public function getPkdoCliente() {
		return $this->pkdoCliente;
	}
	
	public function setPkdoCliente($idCliente) {
		$this->pkdoCliente = $idCliente;
		return $this;
	}
	
	public function getPkdaFilial() {
		return $this->pkdaFilial;
	}
	
	public function setPkdaFilial($idFilial) {
		$this->pkdaFilial = $idFilial;
		return $this;
	}
	
	/**
	 * Consulta os arquivos com base na data de vencimento
	 */
	public function listaArquivosExclusao() {
		
		$vetor = array();
		
		$query = "SELECT * 
				  FROM arquivo 
				 WHERE arquivo.dataExclusao = '".$this->hoje."'
				   AND arquivo.arquivoStatus = '2'";
		
		//print $query; break;
		
		try {
			$o_data = $this->o_db->query($query);
			
			while ($o_ret = $o_data->fetchObject()) {
				$objeto = new ArquivoModel();
				
				$objeto->setPkArquivo($o_ret->pk_arquivo);
				//$objeto->setDescricao($o_ret->descricao);
				//$objeto->setDtInclusao($o_ret->dataInclusao);
				$objeto->setCaminho($o_ret->caminho);
				$objeto->setDtVence($o_ret->dataVence);
				//$objeto->setNotificar($o_ret->notificar);
				//$objeto->setDtExclusao($o_ret->dataExclusao);
				$objeto->setTipo($o_ret->tipo);
				//$objeto->setTamanho($o_ret->tamanho);
				$objeto->setArquivoStatus($o_ret->arquivoStatus);
				$objeto->setNomeArquivo($o_ret->nomeArquivo);
				$objeto->setIdFilialCliente($o_ret->filialCliente_pk_filialCliente);
				
				array_push($vetor,$objeto);
				
				//echo "<pre>"; print_r($vetor); echo "</pre>"; break;
			}

		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $vetor;
	}

	/**
	 * Lista os dados do cliente com base no pk ($idArquivo) repassado.
	 */
	 public function listaDadosCliente($idArquivo) {
	 	
		$vetor = array();
		
		$query = "SELECT arquivo.pk_arquivo, filialCliente.*, cliente.*
				  FROM arquivo
			 INNER JOIN filialCliente ON filialCliente.pk_filialCliente = arquivo.filialCliente_pk_filialCliente
			 INNER JOIN cliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
			 	   AND arquivo.pk_arquivo = ".$idArquivo;
		
		try {
			$o_data = $this->o_db->query($query);
			$o_ret = $o_data->fetchObject();
			
			//$this->setPkCliente($o_ret->pk_cliente);
			$this->setContrato($o_ret->contrato);
			$this->setNomeFantasia($o_ret->fantasia);
			// $this->setRazaoSocial($o_ret->social);
			// $this->setCnpj($o_ret->cnpj);
			// $this->setNomePasta($o_ret->nomePasta);
			// $this->setIdCidade($o_ret->cidade_pk_cidade);
			// $this->setNomeCidade($o_ret->cidade);
			// $this->setClienteStatus($o_ret->clienteStatus);
			// $this->setDataModificado($o_ret->clienteStatusAlterado);
			
			return $this;
			
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		return false;
	 }

	 /**
	 * Lista os dados da filial com base no pk ($idArquivo) repassado.
	 */
	 public function listaDadosFilial($idArquivo) {
	 	
		$vetor = array();
		
		$query = "SELECT arquivo.pk_arquivo, filialCliente.*, filial.*
				  FROM arquivo
			 INNER JOIN filialCliente ON filialCliente.pk_filialCliente = arquivo.filialCliente_pk_filialCliente
			 INNER JOIN filial ON filialCliente.filial_pk_filial = filial.pk_filial
			 	   AND arquivo.pk_arquivo = ".$idArquivo;
		
		try {
			$o_data = $this->o_db->query($query);
			$o_ret = $o_data->fetchObject();
			
			$this->setFilial($o_ret->filial);
			
			return $this;
			
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		return false;
	 }
	
	 /**
	  * Desativa o arquivo ($idArquivo) alterando o seu status de 1 para 2 
	  */
	  public function excluiArquivo($idArquivo) {
		
		$query = "DELETE FROM arquivo
				 WHERE arquivo.pk_arquivo = ".$idArquivo."
				   AND arquivo.arquivoStatus = '2'
				   AND arquivo.dataExclusao = '".$this->hoje."'";
		
		//print $query; break;
		
		try {
			if ($this->o_db->exec($query)) {
				return true;
			}
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
		return false;
	  }
 }
?>