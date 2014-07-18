<?php
/*
 * Gera o log de envio das fotos.
 * Para cada foto é apresentado os dados do processo de envio.
 */
 
 class GeraLogEnvioFotos {
 	
	private $logEnviaArquivo;
	private $nomeArquivoEnviado;
	private $logRenomeiaArquivo;
	private $logRedimensionaArquivo;
	private $logCriaMiniatura;
	private $logSalvaEmBD;
	private $logContaArquivos;
	
	public function __construct() {
		
	}
	
	public function getLogEnviaArquivo() {
	  return $this->logEnviaArquivo;
	}
	
	public function setLogEnviaArquivo($logEnviar) {
	  $this->logEnviaArquivo = $logEnviar;
	  return $this;
	}
	
	public function getLogNomeArquivoEnviado() {
	  return $this->nomeArquivoEnviado;
	}
	
	public function setLogNomeArquivoEnviado($logNomeArquivo) {
	  $this->nomeArquivoEnviado = $logNomeArquivo;
	  return $this;
	}
	
	public function getLogRenomeiaArquivo() {
	  return $this->logRenomeiaArquivo;
	}
	
	public function setLogRenomeiaArquivo($logRenomeia) {
	  $this->logRenomeiaArquivo = $logRenomeia;
	  return $this;
	}
	
	public function getLogRedimensionaArquivo() {
	  return $this->logRedimensionaArquivo;
	}
	
	public function setLogRedimensionaArquivo($redimensionaArquivo) {
	  $this->logRedimensionaArquivo = $redimensionaArquivo;
	  return $this;
	}
	
	public function getLogCriaMiniatura() {
	  return $this->logCriaMiniatura;
	}
	
	public function setLogCriaMiniatura($miniatura) {
	  $this->logCriaMiniatura = $miniatura;
	  return $this;
	}
	
	public function getLogSalvaEmBD() {
	  return $this->logSalvaEmBD;
	}
	
	public function setLogSalvaEmBD($salvarBd) {
	  $this->logSalvaEmBD = $salvarBd;
	  return $this;
	}
	
	public function getLogContaArquivos() {
	  return $this->logContaArquivos;
	}
	
	public function setLogContaArquivos($contador) {
	  $this->logContaArquivos = $contador;
	  return $this;
	}
	
	// Funções privadas que geram o log

	public function validaLogs() {
		
		if ($this->logEnviaArquivo == 1) {
			$this->logEnviaArquivo = "Ok";
		} else {
			$this->logEnviaArquivo = "Falhou";
		}
		
		if ($this->nomeArquivoEnviado == "") {
			$this->nomeArquivoEnviado = "Não identificado";
		}
		
		if ($this->logRenomeiaArquivo == 1) {
			$this->logRenomeiaArquivo = "Ok";
		} else {
			$this->logRenomeiaArquivo = "Falhou";
		}
		
		if ($this->logRedimensionaArquivo == 1) {
			$this->logRedimensionaArquivo = "Ok";
		} else {
			$this->logRedimensionaArquivo = "Falhou";
		}
		
		if ($this->logCriaMiniatura == 1) {
			$this->logCriaMiniatura = "Ok";
		} else {
			$this->logCriaMiniatura = "Falhou";
		}
		
		if ($this->logSalvaEmBD == 1) {
			$this->logSalvaEmBD = "Ok";
		} else {
			$this->logSalvaEmBD = "Falhou";
		}
		
	}
	 
 }
?>