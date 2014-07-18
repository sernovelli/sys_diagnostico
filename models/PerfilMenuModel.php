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
 * Arquivo - FilialClienteModel.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class PerfilMenuModel extends PersistModelAbstract {
 	
	private $pkPerfilMenu;
	
	// filial
	private $pkdoPerfil;
	private $nomedoPerfil;
	
	// usuario
	private $pkdoMenu;
	private $nomedoMenu;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	
	public function getPkPerfilMenu() {
		return $this->pkPerfilMenu;
	}
	
	public function setPkPerfilMenu($pkPerfilMenu) {
		$this->pkPerfilMenu = $pkPerfilMenu;
		return $this;
	}
	
	public function getPkdoPerfil() {
		return $this->pkdoPerfil;
	}
	
	public function setPkdoPerfil($pkPerfil) {
		$this->pkdoPerfil = $pkPerfil;
		return $this;
	}
	
	public function getNomedoPerfil() {
		return $this->nomedoPerfil;
	}
	
	public function setNomedoPerfil($perfil) {
		$this->nomedoPerfil = $perfil;
		return $this;
	}
	
	public function getPkdoMenu() {
		return $this->pkdoMenu;
	}
	
	public function setPkdoMenu($pkMenu) {
		$this->pkdoMenu = $pkMenu;
		return $this;
	}
	
	public function getNomedoMenu() {
		return $this->nomedoMenu;
	}
	
	public function setNomedoMenu($menu) {
		$this->nomedoMenu = $menu;
		return $this;
	}
	

	 /**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null, $perfil = "", $menu = "", $status = 0) {
			
		if (!is_null($id))
			$st_query = "SELECT perfil.*, menu.*, perfilMenu.* FROM perfil
						INNER JOIN perfilMenu ON perfilMenu.perfil_pk_perfil = perfil.pk_perfil
						INNER JOIN menu ON perfilMenu.menu_pk_menu = menu.pk_menu
						WHERE perfilMenu.pk_perfilMenu = ".$id.";";
		else
			$st_query = "SELECT perfil.*, menu.*, perfilMenu.* FROM perfil
						INNER JOIN perfilMenu ON perfilMenu.perfil_pk_perfil = perfil.pk_perfil
						INNER JOIN menu ON perfilMenu.menu_pk_menu = menu.pk_menu";
		
		// FILTROS
		// Filtro por perfil
		if ($perfil != "") {
			$st_query .= " AND perfil.perfil LIKE '%".$perfil."%'";
		}
		
		// Filtro por menu
		if ($menu != "") {
			$st_query .= " AND menu.menu LIKE '%".$menu."%'";
		}
		
		// Filtro por status
		if ($status != 0) {
			$st_query .= " AND menu.menuAtivo = ".$status;
		} else {
			$st_query .= " AND menu.menuAtivo = 1";
		}
		
		$st_query .= " ORDER BY perfilMenu.pk_perfilMenu ASC";
		
		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			
			while ($o_ret = $o_data->fetchObject()) {
				
				$objeto = new PerfilMenuModel();
				
				$objeto->setPkPerfilMenu($o_ret->pk_perfilMenu);
				$objeto->setPkdoPerfil($o_ret->perfil_pk_perfil);
				$objeto->setNomedoPerfil($o_ret->perfil);
				$objeto->setPkdoMenu($o_ret->menu_pk_menu);
				$objeto->setNomedoMenu($o_ret->menu);
				
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
		//print $id; break;
		
		$vetor = array();
		$st_query = "SELECT perfil.*, menu.*, perfilMenu.* FROM perfil
						INNER JOIN perfilMenu ON perfilMenu.perfil_pk_perfil = perfil.pk_perfil
						INNER JOIN menu ON perfilMenu.menu_pk_menu = menu.pk_menu
						WHERE perfilMenu.pk_perfilMenu = ".$id.";";
		
		//print $st_query; //break;
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();

			$this->setPkPerfilMenu($o_ret->pk_perfilMenu);
			$this->setPkdoPerfil($o_ret->perfil_pk_perfil);
			$this->setNomedoPerfil($o_ret->perfil);
			$this->setPkdoMenu($o_ret->menu_pk_menu);
			$this->setNomedoMenu($o_ret->menu);
			
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
	
		if (is_null($this->getPkPerfilMenu())) {
			// verifica se já existe um registro com os dados repassados
			$st_query = "SELECT * FROM perfilMenu 
						WHERE menu_pk_menu = ".$this->getPkdoMenu()."
						AND perfil_pk_perfil = ".$this->getPkdoPerfil();
			
			$o_data = $this->o_db->query($st_query);
			
			if ($o_data->fetchObject() > 0) {
				return -1;
			}
			
			$st_query = "INSERT INTO perfilMenu
							(pk_perfilMenu,
							perfil_pk_perfil,
							menu_pk_menu)
					   VALUES ('$this->pkPerfilMenu',
							'$this->pkdoPerfil',
							'$this->pkdoMenu');";
		
		//echo $st_query; break;
		} else {
			//print $this->pkFilialUsuario; break;
			
			$st_query = "UPDATE perfilMenu 
						 SET	perfil_pk_perfil = '$this->pkdoPerfil',
							menu_pk_menu = '$this->pkdoMenu'
					    WHERE pk_perfilMenu = '$this->pkPerfilMenu'";
		
		//echo $st_query; //break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkPerfilMenu)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_perfilMenu')->fetchObject();
					return $o_ret->pk_perfilMenu;
				} else {
					return $this->pkPerfilMenu;
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
		
		if (!is_null($this->pkPerfilMenu)) {
			$st_query = "DELETE FROM perfilMenu
						WHERE pk_perfilMenu = $this->pkPerfilMenu";
			
			if ($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
