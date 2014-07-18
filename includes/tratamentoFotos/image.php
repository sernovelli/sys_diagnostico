<?php
/**
* PPThumb
*
* Classe para criação de thumbs em paisagem, retrato e quadradas, com ou sem crop.
* @author Lucas Peperaio <lucas_peperaio@hotmail.com>
* @copyright Lucas Peperaio <http://www.lucaspeperaio.com.br>
* @version 1.3
* @since 27/07/2012
*

Changelog
* 1.0: Criada a classe
* 1.1: Adicionado o crop para imagens em paisagem, retrato e quadradas
* 1.2: Adicionado o suporte a múltiplas thumbs
* 1.3: Corrigido bug que permitia thumbs maiores do que o tamanho original
*/
abstract class image{
	private $image;
	private $name;
	
	private $folder;
	private $infoImage;
	private $path;
	
	private $jpegQuality;
	private $pngQuality;
	private $gifQuality;
	
	public function getImage(  ) {
		return $this->image;
	}
	
	public function setImage( $image ) {
		$this->image = $image;
	}
	
	public function getName( ) { 
		return $this->name;
	}
	
	public function setName( $name ) {
		$this->name = $name;
	}
	
	public function getFolder(  ) {
		return $this->folder;
	}
	
	public function setFolder( $folder ) {
		$this->folder = $folder;
	}
	
	public function setInfoImage( $infoImage ) {
		$this->infoImage = getimagesize( $infoImage );
	}
	
	public function getInfoImage( $index = null ) {
		return ( is_numeric( $index ) and array_key_exists( $index, $this->infoImage ) ) ? $this->infoImage[$index] : $this->infoImage;
	}
	
	public function setJpegQuality( $quality ) {
		$this->jpegQuality = ( $quality > 0 and $quality <= 100 ) ? $quality : "100";
	}
	
	public function getJpegQuality( ) {
		return $this->jpegQuality;
	}
	
	public function setPngQuality( $quality ) {
		$this->pngQuality = ( $quality > 0 and $quality <= 9 ) ? $quality : "9";
	}
	
	public function getPngQuality( ) {
		return $this->pngQuality;
	}
	
	public function setGifQuality( $quality ) {
		$this->gifQuality = ( $quality > 0 and $quality <= 100 ) ? $quality : "100";
	}
	
	public function getGifQuality( ) {
		return $this->gifQuality;
	}
	
	public function getMimeType( ) {
		return $this->infoImage['mime'];
	}
	
	public function readImage(){
		//se o script estiver na mesma pasta da imagem
		if( file_exists( $this->getImage( ) ) ) {
			$this->setInfoImage( $this->getImage( ) );
		} else {
			//caso contrário, acessar a imagem através do caminho completo (pasta/imagem)
			$this->setInfoImage( $this->getFolder( ) . "/" . $this->getImage( ) );
			$this->setImage( $this->getFolder( ) . "/" . $this->getImage( ) );
		}
	}
}
?>