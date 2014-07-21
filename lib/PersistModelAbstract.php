<?php
 /**
 * Diret�rio Pai - lib
 * Arquivo - PersistModelAbstract.php
 * @author tarcisioruas
 *
 */
abstract class PersistModelAbstract
{
	/**
	* Vari�vel respons�vel por guardar dados da conex�o do banco
	* @var resource
	*/
	protected $o_db;
	
	function __construct() {
		try {
			// localhost - Desenvolvimento
			$this->o_db = new PDO('mysql:host=localhost;dbname=nome_do_banco', 'root', '', array(PDO::ATTR_PERSISTENT => true)) or print (mysql_error());
			
			$this->o_db->setAttribute ( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
		} catch (PDOException  $e) {
			echo "Atencao: Ocorreu um problema: ".$e->getMessage();
		}
	}
}
?>
