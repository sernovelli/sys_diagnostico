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
 * Arquivo - FotoModel.php
 *
 * @author Sergio Novelli
 * @version 1.0
 *
 */
class FotoModel extends PersistModelAbstract {

	private $pkFoto;
	private $loja;
	private $coordenador;
	private $promotor;
	private $mesReferencia;
	private $dtInclusao;
	private $dtVencimento;
	private $dtExclusao;
	private $foto;
	private $caminhoFoto;
	private $tamanho;
	private $tipo;
	private $fotoStatus;
	private $idFilialCliente;
	private $notificar;
	private $miniatura;

	// cliente
	private $idCliente;
	private $nomeCliente;

	// paginação
	private $pg;
	private $paginacao;
	private $inicio = 0;
	// a partir de qual registro deve listar
	private $qtRegistrosExibir;
	// quantidade de itens para listar

	function __construct() {
		parent::__construct();
	}

	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */

	// paginação
	public function getPg() {
		return $this -> pg;
	}

	public function setPg($pg) {
		$this -> pg = $pg;
		return $this;
	}

	public function getPaginacao() {
		return $this -> paginacao;
	}

	public function setPaginacao($pag) {
		$this -> paginacao = $pag;
		return $this;
	}

	public function getInicio() {
		return $this -> inicio;
	}

	public function setInicio($inicio) {
		$this -> inicio = $inicio;
		return $this;
	}

	public function getQtRegistrosExibir() {
		return $this -> qtRegistrosExibir;
	}

	public function setQtRegistrosExibir($qt) {
		$this -> qtRegistrosExibir = $qt;
		return $this;
	}

	// campos da tabela.
	public function getPkFoto() {
		return $this -> pkFoto;
	}

	public function setPkFoto($pkFoto) {
		$this -> pkFoto = $pkFoto;
		return $this;
	}

	public function getLoja() {
		return $this -> loja;
	}

	public function setLoja($nomeLoja) {
		$this -> loja = $nomeLoja;
		return $this;
	}

	public function getCoordenador() {
		return $this -> coordenador;
	}

	public function setCoordenador($coordenador) {
		$this -> coordenador = $coordenador;
		return $this;
	}

	public function getPromotor() {
		return $this -> promotor;
	}

	public function setPromotor($nomePromotor) {
		$this -> promotor = $nomePromotor;
		return $this;
	}

	public function getMesRefere() {
		return $this -> mesReferencia;
	}

	public function setMesRefere($mesReferencia) {
		$this -> mesReferencia = $mesReferencia;
		return $this;
	}

	public function getDtInclusao() {
		return $this -> dtInclusao;
	}

	public function setDtInclusao($dataInclusao) {
		$this -> dtInclusao = $dataInclusao;
		return $this;
	}

	public function getFoto() {
		return $this -> foto;
	}

	public function setFoto($foto) {
		$this -> foto = $foto;
		return $this;
	}

	public function getCaminhoFoto() {
		return $this -> caminhoFoto;
	}

	public function setCaminhoFoto($url) {
		$this -> caminhoFoto = $url;
		return $this;
	}

	public function getTamanho() {
		return $this -> tamanho;
	}

	public function setTamanho($tamanho) {
		$this -> tamanho = $tamanho;
		return $this;
	}

	public function getFotoStatus() {
		return $this -> fotoStatus;
	}

	public function setFotoStatus($fotoStatus) {
		$this -> fotoStatus = $fotoStatus;
		return $this;
	}

	public function getDtVencimento() {
		return $this -> dtVence;
	}

	public function setDtVencimento($dataVence) {
		$this -> dtVence = $dataVence;
		return $this;
	}

	public function getDtExclusao() {
		return $this -> dtExclusao;
	}

	public function setDtExclusao($dataExclusao) {
		$this -> dtExclusao = $dataExclusao;
		return $this;
	}

	public function getTipo() {
		return $this -> tipo;
	}

	public function setTipo($tipo) {
		$this -> tipo = $tipo;
		return $this;
	}

	public function getIdFilialCliente() {
		return $this -> idFilialCliente;
	}

	public function setIdFilialCliente($idCliente) {
		$this -> idFilialCliente = $idCliente;
		return $this;
	}

	public function getNotificar() {
		return $this -> notificar;
	}

	public function setNotificar($notifica) {
		$this -> notificar = $notifica;
		return $this;
	}

	public function getMiniatura() {
		return $this -> miniatura;
	}

	public function setMiniatura($miniatura) {
		$this -> miniatura = $miniatura;
		return $this;
	}

	// cliente
	public function getIdCliente() {
		return $this -> idCliente;
	}

	public function setIdCliente($idCliente) {
		$this -> idCliente = $idCliente;
		return $this;
	}

	public function getNomeCliente() {
		return $this -> nomeCliente;
	}

	public function setNomeCliente($nomeCliente) {
		$this -> nomeCliente = $nomeCliente;
		return $this;
	}

	/**
	 * Retorna um array contendo os registros
	 */
	public function _list($id = null) {

		if (!is_null($id)) {
			$st_query = "SELECT foto.* FROM foto
					    WHERE foto.pk_foto = " . $id . " 
					    ORDER BY foto.pk_foto ASC;";
		} else {

			// query principal
			$st_query = "SELECT foto.*, filialCliente.*, cliente.*
						FROM foto
					    INNER JOIN filialCliente ON filialCliente.pk_filialCliente = foto.filialCliente_pk_filialCliente
					    INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente";

			if ($_SESSION['hashid'] == "cliente") {

				$st_query .= " AND filialCliente.cliente_pk_cliente = " . $_SESSION['idCliente'] . "
					    		 AND filialCliente.filial_pk_filial = " . $_SESSION['idFilial'];

			} else if ($_SESSION['hashid'] == "gerentefilial") {

				$st_query .= " AND filialCliente.filial_pk_filial = " . $_SESSION['idFilial'];

			} else if ($_SESSION['hashid'] == "superadmin") {
				//
			}

			$st_query .= " AND foto.fotoStatus = 1 
						ORDER BY foto.pk_foto ASC";
		}

		$numRegistros = $this -> contaRegistros($st_query);
		//echo "<pre>"; print $st_query; echo "</pre>"; break;

		$st_query .= " LIMIT " . $this -> getInicio() . ", " . $this -> getQtRegistrosExibir();
		//echo "<pre>"; print $st_query; echo "</pre>"; break;

		try {
			// a query é executada novamente, mas com limitação de registros (LIMIT).
			$o_data = $this -> o_db -> query($st_query);

			// gera a paginacao HTML baseada nessa query.
			$this -> setPaginacao($this -> geraPaginacao($numRegistros));

			$vetor = array();

			while ($o_ret = $o_data -> fetchObject()) {

				$objeto = new FotoModel();
				$objeto -> setPkFoto($o_ret -> pk_foto);
				$objeto -> setLoja($o_ret -> loja);
				$objeto -> setCoordenador($o_ret -> coordenador);
				$objeto -> setPromotor($o_ret -> promotor);
				$objeto -> setMesRefere($o_ret -> mesReferencia);
				$objeto -> setDtInclusao($o_ret -> dataInclusao);
				$objeto -> setDtVencimento($o_ret -> dataVence);
				$objeto -> setDtExclusao($o_ret -> dataExclusao);
				$objeto -> setFoto($o_ret -> foto);
				$objeto -> setCaminhoFoto($o_ret -> urlFoto);
				$objeto -> setTamanho($o_ret -> tamanhoFoto);
				$objeto -> setTipo($o_ret -> tipoFoto);
				$objeto -> setFotoStatus($o_ret -> fotoStatus);
				$objeto -> setNotificar($o_ret -> notificar);
				$objeto -> setIdFilialCliente($o_ret -> filialCliente_pk_filialCliente);
				$objeto -> setMiniatura($o_ret -> miniatura);
				$objeto -> setIdCliente($o_ret -> pk_cliente);
				$objeto -> setNomeCliente($o_ret -> fantasia);

				array_push($vetor, $objeto);

			}
		} catch(PDOException $e) {
		}

		// adiciona a paginação ao vetor de rotorno.
		array_push($vetor, $this -> getPaginacao());

		return $vetor;
	}

	/**
	 * Retorna um array contendo os registros
	 */
	public function _filtros($status = 1, $filial = 0, $cliente = 0) {
		$st_query = "SELECT foto.*, filialCliente.*, filial.*, cliente.* 
			    		FROM foto 
			    INNER JOIN filialCliente ON foto.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente 
			    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
			    INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente 
			    		 AND foto.fotoStatus = " . $status;

		//print $status; break;

		// FILTROS DO CLIENTE
		if ($_SESSION['hashid'] == "cliente") {
			$st_query .= " AND filialCliente.cliente_pk_cliente = " . $_SESSION['idCliente'];

			// por filial
			if ($filial != 0) {
				$st_query .= " AND filialCliente.filial_pk_filial = " . $filial;
			} else {
				$st_query .= " AND filialCliente.filial_pk_filial = " . $_SESSION['idFilial'];
			}

		} else
		// FILTROS DO GERENTE DA FILIAL
		if ($_SESSION['hashid'] == "gerentefilial") {
			$st_query .= " AND filialCliente.filial_pk_filial = " . $_SESSION['idFilial'];

			// por cliente
			if ($cliente != 0) {
				$st_query .= " AND filialCliente.cliente_pk_cliente = " . $cliente;
			}

		} else if ($_SESSION['hashid'] == "superadmin") {
			// por filial
			if ($filial != 0) {
				$st_query .= " AND filialCliente.filial_pk_filial = " . $filial;
			}

			// por cliente
			if ($cliente != 0) {
				$st_query .= " AND filialCliente.cliente_pk_cliente = " . $cliente;
			}
		}

		$st_query .= " ORDER BY foto.pk_foto DESC";

		$numRegistros = $this -> contaRegistros($st_query);
		//echo "<pre>"; print $st_query; echo "</pre>"; break;

		//print $this->contaRegistros($st_query); break;

		$st_query .= " LIMIT " . $this -> getInicio() . ", " . $this -> getQtRegistrosExibir();
		//echo "<pre>"; print $st_query; echo "</pre>"; break;

		$vetor = array();

		try {
			$o_data = $this -> o_db -> query($st_query);

			// gera a paginacao HTML baseada nessa query.
			$this -> setPaginacao($this -> geraPaginacao($numRegistros));

			while ($o_ret = $o_data -> fetchObject()) {

				$objeto = new FotoModel();

				$objeto -> setPkFoto($o_ret -> pk_foto);
				$objeto -> setLoja($o_ret -> loja);
				$objeto -> setCoordenador($o_ret -> coordenador);
				$objeto -> setPromotor($o_ret -> promotor);
				$objeto -> setMesRefere($o_ret -> mesReferencia);
				$objeto -> setDtInclusao($o_ret -> dataInclusao);
				$objeto -> setDtVencimento($o_ret -> dataVence);
				$objeto -> setDtExclusao($o_ret -> dataExclusao);
				$objeto -> setFoto($o_ret -> foto);
				$objeto -> setCaminhoFoto($o_ret -> urlFoto);
				$objeto -> setTamanho($o_ret -> tamanhoFoto);
				$objeto -> setTipo($o_ret -> tipoFoto);
				$objeto -> setFotoStatus($o_ret -> fotoStatus);
				$objeto -> setNotificar($o_ret -> notificar);
				$objeto -> setIdFilialCliente($o_ret -> filialCliente_pk_filialCliente);
				$objeto -> setMiniatura($o_ret -> miniatura);
				$objeto -> setIdCliente($o_ret -> pk_cliente);
				$objeto -> setNomeCliente($o_ret -> fantasia);

				array_push($vetor, $objeto);
			}
		} catch(PDOException $e) {
		}

		// adiciona a paginação ao vetor de rotorno.
		array_push($vetor, $this -> getPaginacao());

		return $vetor;
	}

	/**
	 * Retorna os dados de um perfil referente
	 * a um determinado Id
	 */
	public function loadById($id) {

		$vetor = array();
		$st_query = "SELECT foto.*, filialCliente.*, cliente.* 
					FROM foto
			    INNER JOIN filialCliente ON filialCliente.pk_filialCliente = foto.filialCliente_pk_filialCliente
			    INNER JOIN cliente ON filialCliente.cliente_pk_cliente = cliente.pk_cliente
				    WHERE foto.pk_foto = " . $id . ";";

		// print $st_query;

		try {
			$o_data = $this -> o_db -> query($st_query);
			$o_ret = $o_data -> fetchObject();

			$this -> setPkFoto($o_ret -> pk_foto);
			$this -> setLoja($o_ret -> loja);
			$this -> setCoordenador($o_ret -> coordenador);
			$this -> setPromotor($o_ret -> promotor);
			$this -> setMesRefere($o_ret -> mesReferencia);
			$this -> setDtInclusao($o_ret -> dataInclusao);
			$this -> setDtVencimento($o_ret -> dataVence);
			$this -> setDtExclusao($o_ret -> dataExclusao);
			$this -> setFoto($o_ret -> foto);
			$this -> setCaminhoFoto($o_ret -> urlFoto);
			$this -> setTamanho($o_ret -> tamanhoFoto);
			$this -> setTipo($o_ret -> tipoFoto);
			$this -> setFotoStatus($o_ret -> fotoStatus);
			$this -> setNotificar($o_ret -> notificar);
			$this -> setIdFilialCliente($o_ret -> filialCliente_pk_filialCliente);
			$this -> setMiniatura($o_ret -> miniatura);
			$this -> setIdCliente($o_ret -> pk_cliente);
			$this -> setNomeCliente($o_ret -> fantasia);

			return $this;
		} catch (PDOException $e) {
		}
		return false;
	}

	/**
	 * Salva dados contidos na instancia da classe na tabela. Se o ID for passado,
	 * um UPDATE ser� executado, caso contr�rio, um INSERT ser� executado
	 */
	public function save() {

		if (is_null($this -> pkFoto)) {
			$st_query = "INSERT INTO foto
							(pk_foto,
							loja,
							coordenador,
							promotor,
							mesReferencia,
							dataInclusao,
							dataVence,
							dataExclusao,
							notificar,
							fotoStatus,
							foto,
							urlFoto,
							tipoFoto,
							tamanhoFoto,							
							filialCliente_pk_filialCliente,
							miniatura)
					   VALUES ('$this->pkFoto',
							'$this->loja',
							'$this->coordenador',
							'$this->promotor',
							'$this->mesReferencia',
							'$this->dtInclusao',
							'$this->dtVence',
							'$this->dtExclusao',
							'$this->notificar',
							'$this->fotoStatus',
							'$this->foto',
							'$this->caminhoFoto',
							'$this->tipo',
							'$this->tamanho',							
							'$this->idFilialCliente',
							'$this->miniatura');";

			//echo $st_query; break;
		} else {
			$st_query = "UPDATE foto 
						 SET	loja = '$this->loja',
							coordenador = '$this->coordenador',
							promotor = '$this->promotor',
							mesReferencia = '$this->mesReferencia',
							dataInclusao = '$this->dtInclusao',
							dataVence = '$this->dtVence',
							dataExclusao = '$this->dtExclusao',
							notificar = '$this->notificar',
							fotoStatus = '$this->fotoStatus',
							foto = '$this->foto',
							urlFoto = '$this->caminhoFoto',
							tipoFoto = '$this->tipo',
							tamanhoFoto = '$this->tamanho',
							filialCliente_pk_filialCliente = '$this->idFilialCliente',
							miniatura = '$this->miniatura'
					    WHERE pk_foto = '$this->pkFoto'";

			//echo $st_query; break;
		}

		try {
			if ($this -> o_db -> exec($st_query) > 0)

				if (is_null($this -> pkFoto)) {
					$o_ret = $this -> o_db -> query('SELECT LAST_INSERT_ID() AS pk_foto') -> fetchObject();
					return $o_ret -> pk_foto;
				} else {
					return $this -> pkFoto;
				}
		} catch (PDOException $e) {
			throw $e;
		}
		return false;
	}
	
	/**
	 * Altera o campo 'notificarFotos' na tabela cliente
	 * para que o sistema envie o e-mail de notificação de novas publicações.
	 */
	public function ativaNotificarFotos($idCliente) {

		$st_query = "UPDATE cliente 
					 SET	notificarFotos = '1'
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
	 * Publica/despublica um arquivo conforme id da classe.
	 */
	public function publicar() {

		$status = ($this -> fotoStatus == 1) ? 2 : 1;

		$st_query = "UPDATE foto 
					 SET	fotoStatus = '$status'
				    WHERE pk_foto = '$this->pkFoto'";

		// print $st_query; break;

		try {
			if ($this -> o_db -> exec($st_query) > 0)
				return true;
		} catch (PDOException $e) {
			throw $e;
		}
		return false;
	}

	/**
	 * Deleta os dados persistidos na tabela
	 * usando como referencia o id da classe.
	 */
	public function delete() {
		//print $this->pkFoto; break;

		if (!is_null($this -> pkFoto)) {
			$st_query = "DELETE FROM foto
					    WHERE pk_foto = $this->pkFoto";

			if ($this -> o_db -> exec($st_query) > 0)
				return true;
		}
		return false;
	}

	/**
	 * Método para calcular quantos registros há no total,
	 * sendo executado sem limitação de registros (LIMIT).
	 */
	private function contaRegistros($st_query) {
		//		 echo "<pre>"; print $st_query; echo "</pre>"; break;

		// executa a query sem limitação para gerar a paginação
		$o_data = $this -> o_db -> query($st_query);

		// Conta qtos registros a consulta retornou
		$o_ret = $o_data -> fetchAll(PDO::FETCH_ASSOC);
		$numRegistros = count($o_ret);

		//print $numRegistros; break;

		return $numRegistros;
	}

	/**
	 * Método para gerar a paginação do módulo
	 */
	private function geraPaginacao($reg) {
		require_once "includes/PaginacaoFotos.php";

		$Paginacao = new Paginacao();

		// se pg não foi indicada na url, exibe a primeira pagina
		// caso contrário, exibe a pagina passada por GET.
		$pag_atual = empty($this -> pg) ? 1 : (int)$this -> pg;

		// número de registros que deseja exibir por pagina
		$num_reg_lin = ($this -> getQtRegistrosExibir() == 0) ? 1 : $this -> getQtRegistrosExibir();

		// calcula a quantidade de paginas arredondando para cima
		$ultima_pag = ceil((int)$reg / (int)$num_reg_lin);

		// retorna o html da paginação
		return $Paginacao -> MontarPaginacao($pag_atual, $ultima_pag);
	}

}
?>
