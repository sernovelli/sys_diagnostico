<?php
/**
 * 
 * Responsavel por gerenciar o fluxo de dados entre
 * a camada de modelo e a de visualizacao
 * 
 * Camada - Controladores ou Controllers
 * Diretorio Pai - controllers
 * Arquivo - InicioController.php
 * 
 * @author Sergio Novelli
 * @version 1.0
 *
 */
 class MenuModel extends PersistModelAbstract {
 	
	private $pkMenu;
	private $menu;
	private $url;
	private $menuAtivo;
	private $ordem;
	private $pkMenuPai;
	private $nomeMenuPai;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Setters e Getters da
	 * classe PerfilModel
	 */
	 
	 public function getPkMenu() {
	 	return $this->pkMenu;
	 }
	 
	 public function setPkMenu($pkMenu) {
	 	$this->pkMenu = $pkMenu;
		return $this;
	 }
	 
	 public function getMenu() {
	 	return $this->menu;
	 }
	 
	 public function setMenu($menu) {
	 	$this->menu = $menu;
		return $this;
	 }
	 
	 public function getUrl() {
	 	return $this->url;
	 }
	 
	 public function setUrl($url) {
	 	$this->url = $url;
		return $this;
	 }
	 
	 public function getMenuAtivo() {
	 	return $this->menuAtivo;
	 }
	 
	 public function setMenuAtivo($menuAtivo) {
	 	$this->menuAtivo = $menuAtivo;
		return $this;
	 }
	 
	 public function getOrdem() {
	 	return $this->ordem;
	 }
	 
	 public function setOrdem($ordem) {
	 	$this->ordem = $ordem;
		return $this;
	 }
	 
	 public function getMenuPai() {
	 	return $this->pkMenuPai;
	 }
	 
	 public function setMenuPai($menuPai) {
	 	$this->pkMenuPai = $menuPai;
		return $this;
	 }
	 
	 public function getNomeMenuPai() {
	 	return $this->nomeMenuPai;
	 }
	 
	 public function setNomeMenuPai($nomeMenuPai) {
	 	$this->nomeMenuPai = $nomeMenuPai;
		return $this;
	 }

	 /**
	* Retorna um array contendo os registros
	*/
	public function _list($id = null, $menu = "") {
			
		if (!is_null($id)) {
			$st_query = "SELECT * FROM menu WHERE pk_menu LIKE '%$id%';";
		} else {
			$st_query = "SELECT * FROM menu";
		}
		
		if ($menu != "") {
			$st_query .= " WHERE menu.menu LIKE '%".$menu."%'"; 
		}
		
		//echo "<pre>"; print $st_query; echo "</pre>"; break;
		
		$vetor = array();
		
		try {
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject()) {
				$objeto = new MenuModel();
				$objeto->setPkMenu($o_ret->pk_menu);
				$objeto->setMenu($o_ret->menu);
				$objeto->setUrl($o_ret->url);
				$objeto->setMenuAtivo($o_ret->menuAtivo);
				$objeto->setOrdem($o_ret->ordem);
				$objeto->setMenuPai($o_ret->pk_menu_pai);

				array_push($vetor,$objeto);
			}
		}
		catch(PDOException $e) { }
		return $vetor;
	}
	
	/**
	 *  Esta query é utilizada para o SELECT do menu pai.
	 */
	public function listaMenuPai() {
		$st_query = 'SELECT menu.* FROM menu 
					WHERE menu.pk_menu_pai = 0;';
		
		$vetor = array();
		try {
			$o_data = $this->o_db->query($st_query);
			while($o_ret = $o_data->fetchObject()) {
				$objeto = new MenuModel();
				
				$objeto->setPkMenu($o_ret->pk_menu);
				$objeto->setMenu($o_ret->menu);
				$objeto->setUrl($o_ret->url);
				$objeto->setMenuAtivo($o_ret->menuAtivo);
				$objeto->setOrdem($o_ret->ordem);
				$objeto->setMenuPai($o_ret->pk_menu_pai);
				array_push($vetor,$objeto);
			}
		} catch(PDOException $e) { }
		
		return $vetor;
	}
	
	 /**
	* Retorna um array contendo os registros
	*/
	public function menuVertical($perfil) {
			
			$st_query = "SELECT menu.*, perfilMenu.*, perfil.* FROM menu 
						INNER JOIN perfilMenu ON menu.pk_menu = perfilMenu.menu_pk_menu
						INNER JOIN perfil ON perfil.pk_perfil = perfilMenu.perfil_pk_perfil
						WHERE menu.pk_menu_pai = 0
						AND perfilMenu.consultar = 1
						AND perfil.hashid LIKE '".$perfil."';";
		
		//print $st_query;
		
		$vetor = array();
		try {
			$o_data = $this->o_db->query($st_query);
			while ($o_ret = $o_data->fetchObject()) {
				$objeto = new MenuModel();
				$objeto->setPkMenu($o_ret->pk_menu);
				$objeto->setMenu($o_ret->menu);
				$objeto->setUrl($o_ret->url);
				$objeto->setMenuAtivo($o_ret->menuAtivo);
				$objeto->setOrdem($o_ret->ordem);
				$objeto->setMenuPai($o_ret->pk_menu_pai);
				array_push($vetor,$objeto);
				
				$sqlSubmenu = "SELECT menu.*, perfilMenu.*, perfil.* FROM menu 
							INNER JOIN perfilMenu ON menu.pk_menu = perfilMenu.menu_pk_menu
							INNER JOIN perfil ON perfil.pk_perfil = perfilMenu.perfil_pk_perfil
							WHERE menu.pk_menu_pai = ".$o_ret->pk_menu."
							AND perfilMenu.consultar = 1
							AND perfil.hashid LIKE '".$perfil."';";
							
				$data = $this->o_db->query($sqlSubmenu);
				
				while ($row = $data->fetchObject()) {
					$objeto2 = new MenuModel();
					$objeto2->setPkMenu($row->pk_menu);
					$objeto2->setMenu($row->menu);
					$objeto2->setUrl($row->url);
					$objeto2->setMenuAtivo($row->menuAtivo);
					$objeto2->setOrdem($row->ordem);
					$objeto2->setMenuPai($row->pk_menu_pai);
					array_push($vetor,$objeto2);
				}
				
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
		$st_query = "SELECT * FROM menu WHERE pk_menu LIKE '".$id."';";
		
		try 
		{
			$o_data = $this->o_db->query($st_query);
			$o_ret = $o_data->fetchObject();
			$this->setPkMenu($o_ret->pk_menu);
			$this->setMenu($o_ret->menu);
			$this->setUrl($o_ret->url);
			$this->setMenuAtivo($o_ret->menuAtivo);
			$this->setOrdem($o_ret->ordem);
			$this->setMenuPai($o_ret->pk_menu_pai);
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
		
		if (is_null($this->pkMenu)) {
			$st_query = "INSERT INTO menu
							(pk_menu,
							menu,
							url,
							menuAtivo,
							ordem,
							pk_menu_pai)
					   VALUES ('$this->pkMenu',
							'$this->menu',
							'$this->url',
							'$this->menuAtivo',
							'$this->ordem',
							'$this->pkMenuPai');";
		//echo $st_query; break;
		} else {
			$st_query = "UPDATE menu 
						 SET	menu = '$this->menu',
							url = '$this->url',
							menuAtivo = '$this->menuAtivo',
							ordem = '$this->ordem',
							pk_menu_pai = '$this->pkMenuPai'
					    WHERE pk_menu= '$this->pkMenu'";
		
		//echo $st_query; //break;
		}
		
		try {
			if ($this->o_db->exec($st_query) > 0)
			
				if (is_null($this->pkMenu)) {
					$o_ret = $this->o_db->query('SELECT LAST_INSERT_ID() AS pk_menu')->fetchObject();
					// 
					return $o_ret->pk_menu;
				} else {
					return $this->pkMenu;
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
		
		if (!is_null($this->pkMenu)) {
			$st_query = "DELETE FROM
							menu
						WHERE pk_menu = $this->pkMenu";
			if($this->o_db->exec($st_query) > 0)
				return true;
		}
		return false;
	}

 }
 
 ?>
