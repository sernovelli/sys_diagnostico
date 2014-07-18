<?php


class thumb extends browser {
	public $crop;
	public $sufix;
	private $dimensions;
	private $x;
	private $y;
	private $stream;
	private $thumbWidth;
	private $thumbHeight;
	private $resource;
	
	public function __construct( $image ) {
		image::setImage( $image );
		image::setJpegQuality( 100 );
		image::setPngQuality( 9 );
		image::setGifQuality( 100 );
		browser::showBrowser( true );
		browser::forceDownload( false );
	}
	
	public function setDimensions( array $dimensions ) {
		$this->dimensions = $dimensions;
	}
	
	public function getDimensions( ) {
		return $this->dimensions;
	}
	
	public function process( ) {
		image::readImage(); //ler a imagem
		$thumb = null;
		
		//se for múltiplas thumbs
		if( isset( $thumb[0][0] ) ) {
			$thumb = $this->getDimensions();
		} else {
			$thumb[] = $this->getDimensions();
		}

		foreach( $thumb as $t ) {
			
			// altura proporcional
			if( is_numeric( $t[0] ) and empty( $t[1] ) ) {
				$this->crop = isset( $t[2] ) ? true : false; // caso queira cropar imagens proporcionais, que por padrão são apenas redimensionadas
				$this->thumbWidth = $t[0];
				$this->thumbHeight = round( ( image::getInfoImage( 1 ) * $this->thumbWidth ) / image::getInfoImage( 0 ) ); 
			} 
			
			// largura proporcional
			else if( empty( $t[0] ) and is_numeric( $t[1] ) ) {
				$this->crop = isset( $t[2] ) ? true : false;
				$this->thumbHeight = $t[1];
				$this->thumbWidth = round( ( image::getInfoImage( 0 ) * $this->thumbHeight ) / image::getInfoImage( 1 ) ); 
			}
			
			// altura e largura setados
			else if( is_numeric( $t[0] ) and is_numeric( $t[1] ) ) {
				$this->crop = $this->crop;
				$this->thumbWidth = $t[0];
				$this->thumbHeight = $t[1];
			}
			
			// Só gerar a thumb caso a imagem seja grande o suficiente
			// Se a largura da thumb for maior que a original, reduzí-la até o tamanho original
			// Se a altura da thumb for maior que a original, reduzí-la até o tamanho original
			$this->thumbWidth = ( $this->thumbWidth > image::getInfoImage( 0 ) ) ? image::getInfoImage( 0 ) : $this->thumbWidth;
			$this->thumbHeight = ( $this->thumbHeight > image::getInfoImage( 1 ) ) ? image::getInfoImage( 1 ) : $this->thumbHeight;

			$this->makeThumb();
		}
	}
	
	private function sufix() {

		//Sufixo do nome do arquivo, se for setado o nome do arquivo é alterado
		if( $this->sufix ) { 
			preg_match( "/(.*)(\.[a-z0-9]+)$/", image::getImage(), $r );
			$nome = $r[1];
			$extensao = $r[2];
			image::setName( $nome . "_mini" . $extensao );
		} else {
			//sem sufixo, sobrescrever a imagem
			image::setName( image::getImage() );
		}
	}
	
	private function imageCreate( ) {
		switch( image::getMimeType() ){
			case "image/jpeg": $this->stream = imagecreatefromjpeg( image::getImage() ); break;
			case "image/png": $this->stream = imagecreatefrompng( image::getImage() ); break;
			case "image/gif": $this->stream = imagecreatefromgif( image::getImage() ); break;
			default: $this->stream = imagecreatefromjpeg( image::getImage() );
		}
		
		$this->y = imagesy( $this->stream );
		$this->x = imagesx( $this->stream );
			
		//Salvar corretamente as transparências do PNG
		if( image::getMimeType() == 'image/png' ) {
			$trueColor = imageistruecolor( $this->stream );
			
			//Se a imagem é uma True Color
			if( $trueColor ) {
				$this->resource = imagecreatetruecolor( $this->thumbWidth, $this->thumbHeight );
				imagealphablending( $this->resource, false );
				imagesavealpha( $this->resource, true );
			//Se a imagem é indexada
			} else {
				$this->resource = imagecreate( $this->thumbWidth, $this->thumbHeight );
				imagealphablending( $this->resource, false );
				$transparent = imagecolorallocatealpha( $this->resource, 0, 0, 0, 127 );
				imagefill( $this->resource, 0, 0, $transparent );
				imagesavealpha( $this->resource, true );
				imagealphablending( $this->resource, true );
			}
		//Caso for JPG ou GIF (Não funciona com GIF Animado =P)
		} else {
			$this->resource = imagecreatetruecolor( $this->thumbWidth, $this->thumbHeight );
		}
		
		$this->crop( );
	}
	
	private function crop( ) {
		//Crop?
		//O crop reduzirá a imagem o máximo possível na horizontal (paisagem) ou na vertical (retrato)
		//baseado na proporção, o menor será reduzido pelo maior. Quando a redução chegar ao máximo, será cortada
		//as sobras a fim de obter o meio da imagem
		if( $this->crop ) {
			//dimensões originais
			$largura_original = image::getInfoImage( 0 );
			$altura_original = image::getInfoImage( 1 );
			
			//Eixos iniciais
			$thumbX = 0;
			$thumbY = 0;
			
			//Largura e altura iniciais
			$thumbLargura = $largura_original;
			$thumbAltura = $altura_original;
			
			//Proporção inicial (paisagem, retrato ou quadrado)
			$proporcaoX = $largura_original / $this->thumbWidth;
			$proporcaoY = $altura_original / $this->thumbHeight;

			//Imagem paisagem
			if( $proporcaoX > $proporcaoY ) {
				$thumbLargura = round( $largura_original / $proporcaoX * $proporcaoY );
				$thumbX = round( ( $largura_original - ( $largura_original / $proporcaoX * $proporcaoY ) ) / 2 );
			} 
			//Imagem retrato
			else if( $proporcaoY > $proporcaoX ){
				$thumbAltura = round( $altura_original / $proporcaoY * $proporcaoX );
				$thumbY = round( ( $altura_original - ( $altura_original / $proporcaoY * $proporcaoX ) ) / 2 );
			}
			
			//Cropar a imagem no ponto ideal
			imagecopyresampled ( $this->resource, $this->stream, 0, 0, $thumbX, $thumbY, $this->thumbWidth, $this->thumbHeight, $thumbLargura, $thumbAltura);
		} else { 
			//Apenas reajustar imagem
			imagecopyresampled( $this->resource, $this->stream, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $this->x, $this->y );
		}
		
		browser::prepare( $this->resource );
	}
	
	private function makeThumb( ) {
		//Usuário não quer redimensionar, manter a mesma dimensão
		if( is_null( $this->getDimensions() ) or ( $this->thumbWidth == image::getInfoImage( 0 ) and $this->thumbHeight == image::getInfoImage( 1 ) ) ) {
			$this->thumbWidth = image::getInfoImage( 0 );
			$this->thumbHeight = image::getInfoImage( 1 );
		}
	
		$this->sufix();
		$this->imageCreate();
	}
}
?>