<?php
/**
 * 
 * Esta classe cria um campo SELECT dinâmico
 * para fazer a listagem de itens nos formulários
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - SeletorController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */

class SeletorController {
	private $tabela;
	
	public function seletores($table,$id = 0) {
		
		$this->tabela = $table;
		
		switch ($this->tabela) {
			case 1: {
				return $this->seletorCidades($id);
				break;
			}
			
			case 2: {
				return $this->seletorEstados($id);
				break;
			}
			
			case 3: {
				return $this->seletorPerfis($id);
				break;
			}
			
			case 4: {
				return $this->seletorMenus($id);
				break;
			}
			
			case 5: {
				return $this->seletorUsuarios($id);
				break;
			}
			
			case 6: {
				return $this->seletorFiliais($id);
				break;
			}
			
			case 7: {
				return $this->seletorClientes($id);
				break;
			}
			
			case 8: {
				return $this->seletorMenusTodos($id);
				break;
			}
			
			case 9: {
				return $this->seletorPastas($id);
				break;
			}
			
			case 10: {
				return $this->seletorClientesPorFilial($id);
				break;
			}
			
			case 11: { // Filtro de filial por GERENTE logado
				return $this->seletorFiliaisPorUsuarioLogado($id);
				break;
			}
			
			case 12: { // Filtro de clientes por filial
				return $this->seletorFiltroClientesPorFilial($id);
				break;
			}
			
			case 13: { // Filtro de filiais do CLIENTE logado
				return $this->seletorFiltroFiliaisDoCliente($id);
				break;
			} 
			
			case 14: { // Filtro de filiais do CLIENTE logado
				return $this->seletorFiltroPerfis($id);
				break;
			}
			
			case 15: { // Filtro de filiais do CLIENTE logado
				return $this->seletorFiltroEstados($id);
				break;
			}
			
			case 16: { // Filtro de clientes para as estatsticas
				return $this->seletorEstClientes();
				break;
			}
			
			case 17: { // Filtro de filiais para as estatisticas
				return $this->seletorEstFiliais();
				break;
			}
		}
	}
	
	// seleciona as pastas
	private function seletorPastas($id) {
		require_once 'models/PastaModel.php';
		
		$objeto = new PastaModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='idPasta' id='idPasta' required>";
		$select .= "<option value='' selected='selected'>Selecione</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkPasta() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkPasta()."' ".$selected.">".$item->getPasta()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// seleciona os clientes de acordo com a filial do gerente logado.
	private function seletorClientesPorFilial($id) {
		require_once 'models/ClienteModel.php';
		
		$objeto = new ClienteModel();
		$vetor = $objeto->listarClientePorFilial($id);

		$select = "<select name='idcliente' id='idcliente' required>";
		$select .= "<option value='' selected='selected'>Selecione</option>";
		
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkCliente() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkCliente()."' ".$selected.">".$item->getPkCliente()." - ".$item->getNomeFantasia()."</option>";
		}
		$select .= "</select>";
	
		return $select;
	}
	
	// seleciona os clientes por filial
	private function seletorClientes($id) {
		require_once 'models/ClienteModel.php';
		
		$objeto = new ClienteModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='idcliente' id='idcliente' required>";
		$select .= "<option value='' selected='selected'>Cliente:</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkCliente() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkCliente()."' ".$selected.">".$item->getPkCliente()." - ".$item->getNomeFantasia()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// seleciona os filiais
	private function seletorFiliais($id) {
		require_once 'models/FilialModel.php';
		
		$objeto = new FilialModel();
		$vetor = $objeto->_list();
		
		//print "filial: ".$id; break;
		
		$select = "<select name='idfilial' id='idfilial' required>";
		$select .= "<option value='' selected='selected'>Selecione</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkFilial() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkFilial()."' ".$selected.">".$item->getPkFilial()." - ".$item->getFilial()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// seleciona os usuários
	private function seletorUsuarios($id) {
		require_once 'models/UsuarioModel.php';
		
		$objeto = new UsuarioModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='idusuario' id='idusuario' required>";
		$select .= "<option value='' selected='selected'>Selecione</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkUsuario() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkUsuario()."' ".$selected.">".$item->getPkUsuario()." - ".$item->getNome()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}

	
	// Seleciona os itens de Menu para listar
	// apenas os itens raiz de menus
	private function seletorMenus($id) {
		require_once 'models/MenuModel.php';
		
		$objeto = new MenuModel();
		$vetor = $objeto->listaMenuPai(); //_list(); // não usa o list() pois exibe apenas os itens raiz do menu (0).
		
		$select = "<select name='menuPai' id='menuPai' required>";
		$select .= "<option value='0' selected='selected'>Raiz</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkMenu() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkMenu()."' ".$selected.">".$item->getMenu()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// Seleciona todos os itens de Menu
	private function seletorMenusTodos($id) {
		require_once 'models/MenuModel.php';
		
		$objeto = new MenuModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='idmenu' id='idmenu' required>";
		$select .= "<option value='0' selected='selected'>Selecione</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkMenu() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkMenu()."' ".$selected.">".$item->getMenu()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// Seleciona os Perfis
	private function seletorPerfis($id) {
		require_once 'models/PerfilModel.php';
	
		$objeto = new PerfilModel();
		$vetor = $objeto->_listSelector();
		
		$select = "<select name='idPerfil' id='idPerfil' required>";
		$select .= "<option value='0' selected='selected'>Selecione</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkPerfil() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkPerfil()."' ".$selected.">".$item->getPerfil()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// Seleciona os Estados
	private function seletorEstados($id) {
		require_once 'models/EstadoModel.php';

		$objeto = new EstadoModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='idEstado' id='idEstado' required>";
		$select .= "<option value='0' selected='selected'>Selecione</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkEstado() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkEstado()."' ".$selected.">".$item->getSigla()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// Seleciona as Cidades
	private function seletorCidades($id) {
		require_once 'models/CidadeModel.php';
		
		$objeto = new CidadeModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='idCidade' id='idCidade' required>";
		$select .= "<option value='0' selected='selected'>Selecione</option>";
		foreach ($vetor as $item) {
			
			$selected = ($item->getPkCidade() == $id) ? "selected='selected'" : $selected = "";
			
			$select .= "<option value='".$item->getPkCidade()."' ".$selected.">".$item->getCidade()." - ".$item->getSigla()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// seleciona os filiais de acordo com o GERENTE logado.
	private function seletorFiliaisPorUsuarioLogado($id) {
		require_once 'models/FilialModel.php';
		
		$objeto = new FilialModel();
		$vetor = $objeto->listaFiliaisPorUsuario($id);
		
		$select = "<select name='filial' id='filial'>";
		$select .= "<option value='' selected='selected'>Filial:</option>";
		foreach ($vetor as $item) {
			$select .= "<option value='".$item->getPkFilial()."'>".$item->getPkFilial()." - ".$item->getFilial()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// seleciona os clientes por filial para usar nos filtros
	private function seletorFiltroClientesPorFilial($id) {
		require_once 'models/ClienteModel.php';
		
		$objeto = new ClienteModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='cliente' id='cliente'>";
		$select .= "<option value='' selected='selected'>Cliente:</option>";
		foreach ($vetor as $item) {
			$select .= "<option value='".$item->getPkCliente()."'>".$item->getPkCliente()." - ".$item->getNomeFantasia()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// seleciona as filiais do cliente logado para usar nos filtros
	private function seletorFiltroFiliaisDoCliente($id) {
		require_once 'models/FilialModel.php';
		
		$objeto = new FilialModel();
		$vetor = $objeto->listaFilialPorClienteLogado($id);
		
		$select = "<select name='filial' id='filial'>";
		$select .= "<option value='' selected='selected'>Filial:</option>";
		foreach ($vetor as $item) {
			$select .= "<option value='".$item->getPkFilial()."'>".$item->getPkFilial()." - ".$item->getFilial()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// Seleciona os Perfis para campos de filtros no perfil superadmin
	private function seletorFiltroPerfis($id) {
		require_once 'models/PerfilModel.php';
	
		$objeto = new PerfilModel();
		$vetor = $objeto->_filtroPerfis();
		
		$select = "<select name='perfil' id='perfil'>";
		$select .= "<option value='' selected='selected'>Perfil:</option>";
		
		foreach ($vetor as $item) {
			$select .= "<option value='".$item->getHashid()."'>".$item->getPerfil()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// Seleciona os Estados
	private function seletorFiltroEstados($id) {
		require_once 'models/EstadoModel.php';

		$objeto = new EstadoModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='estado' id='estado'>";
		$select .= "<option value='0' selected='selected'>Estado:</option>";
		foreach ($vetor as $item) {
			$select .= "<option value='".$item->getPkEstado()."'>".$item->getSigla()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}

 	/**
	 * Seletores para os filtros das estatisticas
	 */
	 
	 // seleciona os clientes por filial
	private function seletorEstClientes() {
		require_once 'models/ClienteModel.php';
		
		$objeto = new ClienteModel();
		$vetor = $objeto->_list();
		
		$select = "<select name='idcliente' id='idcliente' >";
		$select .= "<option value='0' selected='selected'>Todos</option>";
		foreach ($vetor as $item) {
			$select .= "<option value='".$item->getPkCliente()."'>".$item->getPkCliente()." - ".$item->getNomeFantasia()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
	
	// seleciona os filiais
	private function seletorEstFiliais() {
		require_once 'models/FilialModel.php';
		
		$objeto = new FilialModel();
		$vetor = $objeto->_list();
		
		//print "filial: ".$id; break;
		
		$select = "<select name='idfilial' id='idfilial'>";
		$select .= "<option value='0' selected='selected'>Todas</option>";
		foreach ($vetor as $item) {
			$select .= "<option value='".$item->getPkFilial()."'>".$item->getPkFilial()." - ".$item->getFilial()."</option>";
		}
		$select .= "</select>";
		
		return $select;
	}
}
?>