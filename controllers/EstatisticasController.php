<?php
require_once 'models/EstatisticasModel.php';
/**
 *
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 *
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - EstatisticasController.php
 *
 * @author Sergio Novelli
 * @version 1.0
 *
 */
class EstatisticasController {

	// comando para gerar o formulário após validacao dos dados enviados.
	private $gerarRelatorio = FALSE;

	private $dataInicio;
	private $dataFinal;
	private $nomeFilial;
	private $nomeCliente;
	private $tipoRelatorio;

	private $filtros = array();

	// controle para o qual deve retornar em casos
	// de sucesso e/ou erro nas operações.
	private $controle = "Estatisticas";

	public function indexAction() {
		// redireciona para a página de login caso o acesso seja
		// feito de forma direta, sem informar a ação na URL.
		header('Location: ?controle=Sair&acao=encerraSessao');
	}

	/**
	 * Efetua a manipula��o dos modelos necessarios
	 * para a aprensenta��o da lista de contatos
	 */
	public function listarAction() {

		//definindo qual o arquivo HTML que sera usado
		$o_view = new View('views/ManterEstatisticasView.phtml');

		//Imprimindo codigo HTML
		$o_view -> showContents();
	}

	public function gerarAction() {

		if (count($_POST) > 0) {
			// validar periodo
			if ($this -> validaPeriodo()) {
				$this -> gerarRelatorio = TRUE;
			}

			// validar filial
			$this -> validaFilial();

			// validar cliente
			$this -> validaCliente();

			$objeto = new EstatisticasModel();

			// escolhe o layout a ser exibido
			// cliente especifico e todas as filiais
			if ($this -> nomeCliente != 0 && $this -> nomeFilial == 0) {
				// define o método de seleção do relatório
				$vetor = $objeto -> fotosPorPeriodoeCliente($this -> dataInicio, $this -> dataFinal, $this -> nomeCliente, $this -> nomeFilial);

				// o arquivo HTML que sera usado
				$o_view = new View('views/relatorios/RelFotosPorPeriodoeCliente.php');

			} else
			// todos os clientes e filial especifica
			if ($this -> nomeCliente == 0 && $this -> nomeFilial != 0) {

				// define o método de seleção do relatório
				$vetor = $objeto -> fotosPorPeriodoeFilial($this -> dataInicio, $this -> dataFinal, $this -> nomeCliente, $this -> nomeFilial);

				// definindo qual o arquivo HTML que sera usado
				$o_view = new View('views/relatorios/RelFotosPorPeriodoeFilial.php');

			} else
			// cliente especifico e filial especifica
			if ($this -> nomeCliente != 0 && $this -> nomeFilial != 0) {
				
				// define o método de seleção do relatório
				$vetor = $objeto -> fotosPorPeriodoFilialeClienteEspecifico($this -> dataInicio, $this -> dataFinal, $this -> nomeCliente, $this -> nomeFilial);
				
				// definindo qual o arquivo HTML que sera usado
				$o_view = new View('views/relatorios/RelFotosPorPeriodoFilialeClienteEspecificos.php');
			} else
			// todos os clientes e todas as filiais
			if ($this -> nomeCliente == 0 && $this -> nomeFilial == 0) {
				
				// define o método de seleção do relatório
				$vetor = $objeto -> fotosPorPeriodoFilialeClienteTodos($this -> dataInicio, $this -> dataFinal, $this -> nomeCliente, $this -> nomeFilial);
				
				// definindo qual o arquivo HTML que sera usado
				$o_view = new View('views/relatorios/RelFotosPorPeriodoFilialeClienteTodos.php');
				
			}

			// Passando os dados para a View
			$o_view -> setParams(array('vetor' => $vetor));

			// Imprimindo codigo HTML
			$o_view -> showContents();
		}
	}

	/**
	 * Valida o cliente se este foi selecionado.
	 * Se não foi selecionado, então asusme que deve trazer 'todos'
	 */
	private function validaCliente() {

		if (isset($_POST['idcliente'])) {
			$this -> nomeCliente = $_POST['idcliente'];
		} else {
			$this -> nomeCliente = "0";
		}
	}

	/**
	 * Valida a filial se esta foi selecionada.
	 * Se não foi selecionada, então assume que deve trazer 'todas'
	 */
	private function validaFilial() {

		if (isset($_POST['idfilial'])) {
			$this -> nomeFilial = $_POST['idfilial'];
		} else {
			$this -> nomeFilial = "0";
		}
	}

	/**
	 * Valida as datas do periodo, convertendo para o padrão inglês
	 * e verificando se foram informadas corretamente.
	 */
	private function validaPeriodo() {
		// se informou data de inicio e não informou a data final do periodo
		// assume que a data final é a data atual
		if (isset($_POST['inicio']) && !isset($_POST['final'])) {
			$this -> dataInicio = implode("-", array_reverse(explode("/", $_POST['inicio'])));
			$this -> dataFinal = date("Y-m-d");
		} else

		// se informou as duas datas do periodo: inicial e final
		if (isset($_POST['inicio']) && isset($_POST['final'])) {
			$this -> dataInicio = implode("-", array_reverse(explode("/", $_POST['inicio'])));
			$this -> dataFinal = implode("-", array_reverse(explode("/", $_POST['final'])));
		} else

		// se não informou a data de inicio e informou a data final
		// assume que a data inicial é a data atual
		if (!isset($_POST['inicio']) && isset($_POST['final'])) {
			$this -> dataInicio = date("Y-m-d");
			$this -> dataFinal = implode("-", array_reverse(explode("/", $_POST['final'])));
		} else

		// se não informou o período, automaticamente vai trazer a data atual para ambas as datas.
		if (!isset($_POST['inicio']) && !isset($_POST['final'])) {
			$this -> dataInicio = implode("-", array_reverse(explode("/", $_POST['inicio'])));
			$this -> dataFinal = implode("-", array_reverse(explode("/", $_POST['final'])));
		} else {
			return FALSE;
		}

		// verifica se a data inicial é maior que a data final
		if ($this -> dataInicio > $this -> dataFinal) {
			// erro
		} else {
			return true;
		}
	}

}
?>