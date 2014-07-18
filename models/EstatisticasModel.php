<?php
/**
 *
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 *
 * Camada - Models
 * Diretorio Pai - models
 * Arquivo - EstatisticasModel.php
 *
 * @author Sergio Novelli
 * @version 1.0
 *
 */

require_once "models/FilialModel.php";

class EstatisticasModel extends PersistModelAbstract {

	// filtros
	private $dataInicio;
	private $dataFinal;
	private $fNomeFilial;
	private $fNomeCliente;

	// cliente
	private $pkCliente;
	private $contrato;
	private $nomeCliente;

	// filial
	private $pkFilial;
	private $nomeFilial;

	// fotos
	private $dataInclusao;
	private $totalFotos;
	private $qtdeFotos;
	private $TotalFotosPorFilial;

	function __construct() {
		parent::__construct();
	}

	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */

	public function getDataInicio() {
		return $this -> dataInicio;
	}

	public function setDataInicio($inicio) {
		$this -> dataInicio = $inicio;
		return $this;
	}

	public function getDataFinal() {
		return $this -> dataFinal;
	}

	public function setDataFinal($final) {
		$this -> dataFinal = $final;
		return $this;
	}

	// cliente
	public function getPkCliente() {
		return $this -> pkCliente;
	}

	public function setPkCliente($pkCliente) {
		$this -> pkCliente = $pkCliente;
		return $this;
	}

	public function getContrato() {
		return $this -> contrato;
	}

	public function setContrato($contrato) {
		$this -> contrato = $contrato;
		return $this;
	}

	public function getNomeCliente() {
		return $this -> nomeCliente;
	}

	public function setNomeCliente($cliente) {
		$this -> nomeCliente = $cliente;
		return $this;
	}

	// filial
	public function getPkFilial() {
		return $this -> pkFilial;
	}

	public function setPkFilial($pkFilial) {
		$this -> pkFilial = $pkFilial;
		return $this;
	}

	public function getNomeFilial() {
		return $this -> nomeFilial;
	}

	public function setNomeFilial($filial) {
		$this -> nomeFilial = $filial;
		return $this;
	}

	// fotos
	public function getDataInclusao() {
		return $this -> dataInclusao;
	}

	public function setDataInclusao($inclusao) {
		$this -> dataInclusao = $inclusao;
		return $this;
	}

	public function getTotalFotos() {
		return $this -> totalFotos;
	}

	public function setTotalFotos($total) {
		$this -> totalFotos = $total;
		return $this;
	}

	public function getQtdeFotos() {
		return $this -> qtdeFotos;
	}

	public function setQtdeFotos($numFotos) {
		$this -> qtdeFotos = $numFotos;
		return $this;
	}

	public function getTotalFotosPorFilial() {
		return $this -> TotalFotosPorFilial;
	}

	public function setTotalFotosPorFilial($total) {
		$this -> TotalFotosPorFilial = $total;
		return $this;
	}

	// filtros
	public function getFnomeFilial() {
		return $this -> fNomeFilial;
	}

	public function setFnomeFilial($filial) {
		$this -> fNomeFilial = $filial;
		return $this;
	}

	public function getFnomeCliente() {
		return $this -> fNomeCliente;
	}

	public function setFnomeCliente($cliente) {
		$this -> fNomeCliente = $cliente;
		return $this;
	}

	/**
	 * Retorna os dados de fotos por cliente
	 */
	public function fotosPorPeriodoeCliente($inicio, $final, $idCliente, $idFilial) {

		$st_query = "SELECT cliente.pk_cliente,
						cliente.fantasia,
						cliente.contrato,
						filial.pk_filial,
						filial.filial,
						COUNT(foto.pk_foto) AS numFotos
					FROM foto
			    INNER JOIN filialCliente ON foto.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente
			    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
			    INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente
					 AND cliente.pk_cliente = " . $idCliente . " 
				    WHERE foto.dataInclusao BETWEEN '" . $inicio . "' AND '" . $final . "' 
				 GROUP BY filial.pk_filial 
				 ORDER BY cliente.pk_cliente ASC";

		// echo "<pre>";
		// print $st_query;
		// echo "</pre>";
		// break;

		$vetor = array();

		try {
			$o_data = $this -> o_db -> query($st_query);

			while ($o_ret = $o_data -> fetchObject()) {

				$objeto = new EstatisticasModel();

				// filto cliente
				//$objeto -> setPkCliente($o_ret -> pk_cliente);
				$objeto -> setFnomeCliente($o_ret -> fantasia);

				// filtro filial
				$objeto -> setFnomeFilial("Todas");

				// filiais
				$objeto -> setPkFilial($o_ret -> pk_filial);
				$objeto -> setNomeFilial($o_ret -> filial);

				$objeto -> setQtdeFotos($o_ret -> numFotos);

				// filtro periodo
				$objeto -> setDataInicio($inicio);
				$objeto -> setDataFinal($final);

				array_push($vetor, $objeto);
			}

		} catch(PDOException $e) {
			echo $e -> getMessage();
		}
		return $vetor;
	}

	/**
	 * Retorna os dados de fotos por filial
	 */
	public function fotosPorPeriodoeFilial($inicio, $final, $idCliente, $idFilial) {

		$st_query = "SELECT cliente.pk_cliente,
						cliente.fantasia,
						cliente.contrato,
						filial.pk_filial,
						filial.filial,
						COUNT(foto.pk_foto) AS numFotos
					FROM foto
			    INNER JOIN filialCliente ON foto.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente
			    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
			    INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente
					 AND filial.pk_filial = " . $idFilial . " 
				    WHERE foto.dataInclusao BETWEEN '" . $inicio . "' AND '" . $final . "' 
				 GROUP BY cliente.pk_cliente
				 ORDER BY filial.pk_filial ASC";

		// echo "<pre>";
		// print $st_query;
		// echo "</pre>";
		// break;

		$vetor = array();

		try {
			$o_data = $this -> o_db -> query($st_query);

			while ($o_ret = $o_data -> fetchObject()) {

				$objeto = new EstatisticasModel();

				// filtro cliente
				//$objeto -> setPkCliente($o_ret -> pk_cliente);
				$objeto -> setFnomeCliente("Todos");

				// filtro filial
				$objeto -> setFnomeFilial($o_ret -> filial);

				// clientes
				$objeto -> setPkCliente($o_ret -> pk_cliente);
				$objeto -> setNomeCliente($o_ret -> fantasia);
				$objeto -> setContrato($o_ret -> contrato);

				$objeto -> setQtdeFotos($o_ret -> numFotos);

				// filtro periodo
				$objeto -> setDataInicio($inicio);
				$objeto -> setDataFinal($final);

				array_push($vetor, $objeto);
			}

		} catch(PDOException $e) {
			echo $e -> getMessage();
		}
		return $vetor;
	}

	/**
	 * Retorna os dados de fotos por cliente e filial não especificos (todos/todas)
	 */
	public function fotosPorPeriodoFilialeClienteTodos($inicio, $final, $idCliente, $idFilial) {

		$st_query = "SELECT * FROM filial ORDER BY filial.pk_filial";

		$vetor = array();

		try {
			$o_data = $this -> o_db -> query($st_query);

			$totalFotosFilial = 0;

			while ($o_ret = $o_data -> fetchObject()) {

				$objeto = new FilialModel();

				// filiais
				$objeto -> setPkFilial($o_ret -> pk_filial);
				$objeto -> setFilial($o_ret -> filial);

				//$objeto -> setTotalFotosPorFilial($totalFotosFilial);

				array_push($vetor, $objeto);

				// busca os clientes da filial atual (getPkFilial())
				$st_query2 = "SELECT cliente.pk_cliente,
								cliente.fantasia,
								cliente.contrato,
								filial.pk_filial,
								filial.filial,
								COUNT(foto.pk_foto) AS numFotos
							FROM foto
					    INNER JOIN filialCliente ON foto.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente
					    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
					    INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente
							 AND filial.pk_filial = " . $o_ret -> pk_filial . " 
						    WHERE foto.dataInclusao BETWEEN '" . $inicio . "' AND '" . $final . "' 
						 GROUP BY cliente.pk_cliente, filial.pk_filial
						 ORDER BY filial.pk_filial ASC";

				// echo "<pre>";
				// print $st_query;
				// echo "</pre>";
				// break;

				try {
					$o_data2 = $this -> o_db -> query($st_query2);

					while ($row = $o_data2 -> fetchObject()) {

						$objeto2 = new EstatisticasModel();

						// filtros cliente e filial
						$objeto2 -> setFnomeCliente("Todos");
						$objeto2 -> setFnomeFilial("Todas");

						// filtro periodo
						$objeto2 -> setDataInicio($inicio);
						$objeto2 -> setDataFinal($final);

						// clientes
						$objeto2 -> setPkCliente($row -> pk_cliente);
						$objeto2 -> setNomeCliente($row -> fantasia);
						$objeto2 -> setContrato($row -> contrato);
						$objeto2 -> setQtdeFotos($row -> numFotos);

						$totalFotosFilial += $row -> numFotos;

						array_push($vetor,$objeto2);
					}
				} catch (PDOException $e) {
					echo $e -> getMessage();
				}
			} // fecha while principal

		} catch(PDOException $e) {
			echo $e -> getMessage();
		}
		return $vetor;
	}

	/**
	 * Retorna os dados de fotos por cliente e filial especificos
	 */
	public function fotosPorPeriodoFilialeClienteEspecifico($inicio, $final, $idCliente, $idFilial) {

		$st_query = "SELECT cliente.pk_cliente,
						cliente.fantasia,
						cliente.contrato,
						filial.pk_filial,
						filial.filial,
						COUNT(foto.pk_foto) AS numFotos
					FROM foto
			    INNER JOIN filialCliente ON foto.filialCliente_pk_filialCliente = filialCliente.pk_filialCliente
			    INNER JOIN filial ON filial.pk_filial = filialCliente.filial_pk_filial 
			    		 AND filial.pk_filial = " . $idFilial . "
			    INNER JOIN cliente ON cliente.pk_cliente = filialCliente.cliente_pk_cliente
					 AND cliente.pk_cliente = " . $idCliente . " 
				    WHERE foto.dataInclusao BETWEEN '" . $inicio . "' AND '" . $final . "' 
				 GROUP BY cliente.pk_cliente
				 ORDER BY filial.pk_filial ASC";

		// echo "<pre>";
		// print $st_query;
		// echo "</pre>";
		// break;

		$vetor = array();

		try {
			$o_data = $this -> o_db -> query($st_query);

			while ($o_ret = $o_data -> fetchObject()) {

				$objeto = new EstatisticasModel();

				// filtro cliente
				$objeto -> setFnomeCliente($o_ret -> fantasia);

				// filtro filial
				$objeto -> setFnomeFilial($o_ret -> filial);

				// clientes
				$objeto -> setPkCliente($o_ret -> pk_cliente);
				$objeto -> setNomeCliente($o_ret -> fantasia);
				$objeto -> setContrato($o_ret -> contrato);

				$objeto -> setQtdeFotos($o_ret -> numFotos);

				// filtro periodo
				$objeto -> setDataInicio($inicio);
				$objeto -> setDataFinal($final);

				array_push($vetor, $objeto);
			}

		} catch(PDOException $e) {
			echo $e -> getMessage();
		}
		return $vetor;
	}

}
?>