<?php
/**
 * Classe para realização de download de arquivos (relatórios)
 */
 
class Download {
	
	private $arquivo;
	
	public function __construct($arquivo,$tipo) {
		
		$this->arquivo = $arquivo;
		
		$this->fazDownload($tipo);
	}
	
	/**
	 * realiza o download do arquivo
	 */
	private function fazDownload($tipo) {
		//header("Content-type: application/save"); // salva qualquer tipo (indefinido) de arquivo
		
		if ($tipo == "pdf") {
			header('Content-type: application/pdf'); // salva arquivos em pdf
		}
		
		if ($tipo == "ppt" || $tipo == "pps") {
			header('Content-type: application/vnd.ms-powerpoint'); // salva arquivos em ppt ou pps
		}
		
		if ($tipo == "pptx") {
			header('Content-type: application/vnd.openxmlformats-officedocument.presentationml.presentation'); // salva arquivos em pptx
		}
		
		if ($tipo == "xls") {
			header('Content-type: application/vnd.ms-excel'); // salva arquivos em xls
		}
		
		if ($tipo == "xlsx") {
			header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // salva arquivos em xlsx
		}
		
		if ($tipo == "jpg" || $tipo == "jpeg") {
			header('Content-type: image/jpeg'); // salva arquivos de fotos
		}
		
		header("Content-Length:".filesize($this->arquivo));
		header('Content-Disposition: attachment; filename="'.basename($this->arquivo).'";');
		header('Expires: 0');
		header('Pragma: no-cache');
		
		readfile($this->arquivo);
	}
}

/* headers para download.
    "htm" => "text/html",
    "exe" => "application/octet-stream",
    "zip" => "application/zip",
    "doc" => "application/msword",
    "jpg" => "image/jpg",
    "php" => "text/plain",
    "xls" => "application/vnd.ms-excel",
    "ppt" => "application/vnd.ms-powerpoint",
    "gif" => "image/gif",
    "pdf" => "application/pdf",
    "txt" => "text/plain",
    "html"=> "text/html",
    "png" => "image/png",
    "jpeg"=> "image/jpg"
*/
?>