<?php
 /**
 * Classe designada a filtragem de dados
 * @author DigitalDev
 * @version 0.1.0
 * Diret�rio Pai - lib
 */
class DataFilter {
	/**
	* Retira pontuacao da string 
	* @param string $st_data
	* @return string
	*/
	static function alphaNum( $st_data ) {
		$st_data = preg_replace("([[:punct:]]| )",'',$st_data);
		return $st_data;
	}
	
	/**
	* Retira caracteres nao numericos da string
	* @param string $st_data
	* @return string
	*/
	static function numeric( $st_data ) {
		$st_data = preg_replace("([[:punct:]]|[[:alpha:]]| )",'',$st_data);
		return $st_data;
	}
	
	/**
	 * 
	 * Retira tags HTML / XML e adiciona "\" antes
	 * de aspas simples e aspas duplas
	 * e converte a entrada em MAIÚSCULO
	 */
	static function cleanString( $st_string ) {
		$string = addslashes(strip_tags($st_string));
		return strtoupper($string);
	}
	
	/**
	 * Retira tags HTML / XML e adiciona "\" antes
	 * de aspas simples e aspas duplas deixando a
	 * entrada em minúsculo
	 */
	static function cleanStringMin( $st_string ) {
		return addslashes(strip_tags($st_string));
	}
}
?>