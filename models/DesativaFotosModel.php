<?php

require_once "models/FotoModel.php";

/**
 * Classe para despublicar fotos publicadas a 90 ou mais dias
 */
 
 class DesativaFotosModel extends PersistModelAbstract {
 	
	private $pkFoto;
	private $loja;
	//private $coordenador;
	//private $promotor;
	//private $mesReferencia;
	//private $dtInclusao;
	private $dtVencimento;
	//private $dtExclusao;
	private $foto;
	private $caminhoFoto;
	//private $tamanho;
	private $tipo;
	private $fotoStatus;
	private $idFilialCliente;
	private $notificar;
	private $miniatura;
	
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
	private $hoje = "2013-11-26"; // testes
	

	
	function __construct() {
		parent::__construct();
		$this->hoje = date('Y-m-d', strtotime($this->hoje));
	}
	
	public function getPkFoto() {
		return $this->pkFoto;
	}
	
	public function setPkFoto($pkFoto) {
		$this->pkFoto = $pkFoto;
		return $this;
	}
	
	public function getLoja() {
		return $this->loja;
	}
	
	public function setLoja($nomeLoja) {
		$this->loja = $nomeLoja;
		return $this;
	}
	
	// public function getCoordenador() {
		// return $this->coordenador;
	// }
// 	
	// public function setCoordenador($coordenador) {
		// $this->coordenador = $coordenador;
		// return $this;
	// }
	
	// public function getPromotor() {
		// return $this->promotor;
	// }
// 	
	// public function setPromotor($nomePromotor) {
		// $this->promotor = $nomePromotor;
		// return $this;
	// }
	
	// public function getMesRefere() {
		// return $this->mesReferencia;
	// }
// 	
	// public function setMesRefere($mesReferencia) {
		// $this->mesReferencia = $mesReferencia;
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
	
	public function setCaminhoFoto($url) {
		$this->caminhoFoto = $url;
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
	
	public function getFotoStatus() {
		return $this->fotoStatus;
	}
	
	public function setFotoStatus($fotoStatus) {
		$this->fotoStatus = $fotoStatus;
		return $this;
	}
	
	public function getDtVencimento() {
	  return $this->dtVence;
	}
	
	public function setDtVencimento($dataVence) {
	  $this->dtVence = $dataVence;
	  return $this;
	}
	
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
	
	public function getIdFilialCliente() {
	  return $this->idFilialCliente;
	}
	
	public function setIdFilialCliente($idCliente) {
	  $this->idFilialCliente = $idCliente;
	  return $this;
	}
	
	// public function getNotificar() {
	  // return $this->notificar;
	// }
// 	
	// public function setNotificar($notifica) {
	  // $this->notificar = $notifica;
	  // return $this;
	// }
	
	public function getMiniatura() {
	  return $this->miniatura;
	}
	
	public function setMiniatura($miniatura) {
	  $this->miniatura = $miniatura;
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
	 * Consulta as fotos com base na data de vencimento
	 */
	public function listaFotosVencimento() {
		
		$vetor = array();
		
		$query = "SELECT * 
				  FROM foto 
				 WHERE dataVence = '".$this->hoje."'
				   AND foto.fotoStatus = 1";
		
		//print $query; break;
		
		try {
			$o_data = $this->o_db->query($query);
			
			while ($o_ret = $o_data->fetchObject()) {
				$objeto = new FotoModel();
				
				$objeto->setPkFoto($o_ret->pk_foto);
				$objeto->setLoja($o_ret->loja);
				//$objeto->setCoordenador($o_ret->coordenador);
				//$objeto->setPromotor($o_ret->promotor);
				//$objeto->setMesRefere($o_ret->mesReferencia);
				//$objeto->setDtInclusao($o_ret->dataInclusao);
				$objeto->setDtVencimento($o_ret->dataVence);
				//$objeto->setDtExclusao($o_ret->dataExclusao);
				$objeto->setFoto($o_ret->foto);
				$objeto->setCaminhoFoto($o_ret->urlFoto);
				//$objeto->setTamanho($o_ret->tamanhoFoto);
				$objeto->setTipo($o_ret->tipoFoto);
				$objeto->setFotoStatus($o_ret->fotoStatus);
				//$objeto->setNotificar($o_ret->notificar);
				$objeto->setIdFilialCliente($o_ret->filialCliente_pk_filialCliente);
				$objeto->setMiniatura($o_ret->miniatura);
				
				array_push($vetor,$objeto);
				
				//echo "<pre>"; print_r($vetor); echo "</pre>"; break;
			}

		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $vetor;
	}

	/**
	 * Lista os dados do cliente com base no pk da foto repassado.
	 */
	 public function listaDadosCliente($idFoto) {
	 	
		$vetor = array();
		
		$query = "SELECT foto.pk_foto, filialCliente.*, cliente.*
				  FROM foto
			 INNER JOIN filialCliente ON filialCliente.pk_filialCliente = foto.filialCliente_pk_filialCliente
			 INNER JOIN cliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
			 	   AND foto.pk_foto = ".$idFoto;
		
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
	 * Lista os dados da filial com base no pk da foto repassado.
	 */
	 public function listaDadosFilial($idFoto) {
	 	
		$vetor = array();
		
		$query = "SELECT foto.pk_foto, filialCliente.*, filial.*
				  FROM foto
			 INNER JOIN filialCliente ON filialCliente.pk_filialCliente = foto.filialCliente_pk_filialCliente
			 INNER JOIN filial ON filialCliente.filial_pk_filial = filial.pk_filial
			 	   AND foto.pk_foto = ".$idFoto;
		
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
	  * Desativa a foto ($idFoto) alterando o seu status de 1 para 2 
	  */
	  public function desativaFoto($idFoto) {
		
		$query = "UPDATE foto
				   SET fotoStatus = '2'
				 WHERE foto.pk_foto = ".$idFoto."
				   AND foto.fotoStatus = '1'
				   AND foto.dataVence = '".$this->hoje."'";
		
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