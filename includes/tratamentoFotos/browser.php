<?php


class browser extends image {
	private $showBrowser;
	private $forceDownload;
	private $resource;
	
	public function showBrowser( $show ){
		$this->showBrowser = (bool) $show;
	}
	
	public function forceDownload( $download ) {
		$this->forceDownload = (bool) $download;
	}
	
	public function prepare( $resource ) {
		$this->resource = $resource;
				
		//Usuário não setou a pasta, mostrar no navegador
		if( is_null( image::getFolder( ) ) or !is_dir( image::getFolder( ) ) ) {
			$this->display();
			return false;
		}
		
		if( !$this->showBrowser ) {
			$this->save();
		} else {
			if( $this->forceDownload ) {
				$this->download();
			} else {
				$this->display();
			}
		}
	}
	
	private function output( $path ) {
		switch( image::getMimeType() ) {
			case "image/jpeg": imagejpeg( $this->resource, $path, image::getJpegQuality()); break;
			case "image/png": imagepng( $this->resource, $path, image::getPngQuality()); break;
			case "image/gif": imagegif( $this->resource, $path, image::getGifQuality()); break;
			default: imagejpeg( $this->resource, $path, image::getJpegQuality()); break;
		}
	}
	
	private function download( ) {
		header( "Content-type: " . image::getMimeType() );
		header( "Content-Disposition: attachament; filename = " . $this->resource );
		$this->output( null );
	}
	
	private function display( ) {
		header( "Content-type: " . image::getMimeType() );
		header( "Content-Disposition: inline; filename = " . $this->resource );
		$this->output( null );
	}
		
	private function save( ) {
		$this->output( image::getName() );
	}
		
	public function __destruct( ) {
		imagedestroy( $this->resource );
	}
}