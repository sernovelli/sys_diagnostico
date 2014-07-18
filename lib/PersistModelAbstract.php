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
				
			// servidor remoto - Produção
			 $this->o_db = new PDO('mysql:host=localhost;dbname=dinamica_geraarquivos', 'dinamica_garquiv', '2q5r4dcplm', array(PDO::ATTR_PERSISTENT => true)) or print (mysql_error());
			
			// servidor remoto - Homologação
			// $this->o_db = new PDO('mysql:host=localhost;dbname=dinamirh_geraarquivos', 'dinamirh_garquiv', '2q5r4dcplm', array(PDO::ATTR_PERSISTENT => true)) or print (mysql_error());
			
			// servidor local - Windows 2008 - Testes
			//$this->o_db = new PDO('mysql:host=192.168.10.249;port=3306;dbname=dinamirh_geraarquivos', 'teste', 'teste', array(PDO::ATTR_PERSISTENT => true)) or die(mysql_error());
			
			// localhost - Desenvolvimento
			//$this->o_db = new PDO('mysql:host=localhost;dbname=geraarquivos', 'root', '', array(PDO::ATTR_PERSISTENT => true)) or print (mysql_error());
			
			$this->o_db->setAttribute ( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
		} catch (PDOException  $e) {
			echo "Atencao: Ocorreu um problema: ".$e->getMessage();
		}
	}
}
?>