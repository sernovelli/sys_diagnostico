<?php

class Mensagens {
	private $codMsg;
	
	public function __construct($codMsg) {
		$this->codMsg = $codMsg;
		return $this->geraMensagem();
	}
	
	// getters e setters
	public function getCodMsg() {
		return $this->codMsg;
	}
	
	public function setCodMsg($codMsg) {
		$this->codMsg = $codMsg;
		return $this;
	}
	
	// seleciona mensagem que será exibida.
	public function geraMensagem() {
		switch($this->codMsg) {
			case 1: {
				 $msg = "Registro salvo com sucesso!";
				 $this->msgSucesso($msg);
				 break;
			}
			
			case 2: {
				$msg = "Erro: a operação salvar/atualizar falhou!";
				$this->msgErro($msg);
				break;
			}
			
			case 3: {
				$msg = "Registro excluído com sucesso!";
				$this->msgSucesso($msg);
				break;
			}
			
			case 4: {
				$msg = "Erro ao excluir!";
				$this->msgErro($msg);
				break;
			}
			
			case 5: {
				$msg = "Erro: o código do registro não é um número inteiro válido!";
				$this->msgErro($msg);
				break;
			}
			
			case 6: {
				$msg = "Erro: o código do registro não foi informado!";
				$this->msgErro($msg);
				break;
			}
			
			case 7: {
				$msg = "Erro de navegação: controle não encontrado ou não existe!";
				$this->msgErro($msg);
				break;
			}
			
			case 8: {
				$msg = "Erro de navegação: ação não encontrada ou não existe!";
				$this->msgErro($msg);
				break;
			}
			
			case 9: {
				$msg = "Erro de navegação: classe não encontrada ou não existe!";
				$this->msgErro($msg);
				break;
			}
			
			case 10: {
				$msg = "Erro: usuário incorreto ou não existe!";
				$this->msgErro($msg);
				break;
			}
			
			case 11: {
				$msg = "Erro: senha incorreta ou não existe!";
				$this->msgErro($msg);
				break;
			}
			
			case 12: {
				$msg = "Erro: este não é um usuário ativo!";
				$this->msgErro($msg);
				break;
			}
			
			case 13: {
				$msg = "Erro: o usuário não tem um perfil de acesso!";
				$this->msgErro($msg);
				break;
			}
			
			case 14: {
				$msg = "Erro: selecione o tipo de acesso!";
				$this->msgErro($msg);
				break;
			}
			
			case 15: {
				$msg = "Registro atualizado com sucesso!";
				$this->msgSucesso($msg);
				break;
			}
			
			case 16: {
				$msg = "Já existe um registro com esses dados!";
				$this->msgErro($msg);
				break;
			}
			
			case 17: {
				$msg = "Não foi possível criar a pasta para este cliente.";
				$this->msgErro($msg);
				break;
			}
			
			case 18: {
				$msg = "Erro ao gerar o nome da pasta: os parâmetros necessários são inválidos.";
				$this->msgErro($msg);
				break;
			}
			
			case 19: {
				$msg = "Já existe uma pasta com esse nome.";
				$this->msgErro($msg);
				break;
			}
			
			case 20: {
				$msg = "Erro ao criar a pasta do cliente.";
				$this->msgErro($msg);
				break;
			}
			
			case 21: {
				$msg = "Erro: o campo 'nome fantasia' está nullo ou vazio.";
				$this->msgErro($msg);
				break;
			}
			
			case 22: {
				$msg = "Erro: o campo 'contrato' está nullo ou vazio.";
				$this->msgErro($msg);
				break;
			}
			
			case 23: {
				$msg = "Erro: não foi possível excluir a pasta desse cliente.";
				$this->msgErro($msg);
				break;
			}
			
			case 24: {
				$msg = "Erro: a pasta desse cliente não foi encontrada para exclusão.";
				$this->msgErro($msg);
				break;
			}
			
			case 25: {
				$msg = "Erro: a pasta desse cliente não foi encontrada para criar a estrutura base de diretórios.";
				$this->msgErro($msg);
				break;
			}
			
			case 26: {
				$msg = "Erro: não foi possível criar a pasta da filial.";
				$this->msgErro($msg);
				break;
			}
			
			case 27: {
				$msg = "Erro: não foi possível criar a pasta 'relatorios'.";
				$this->msgErro($msg);
				break;
			}
			
			case 28: {
				$msg = "Erro: não foi possível criar a pasta 'fotos'.";
				$this->msgErro($msg);
				break;
			}
			
			case 29: {
				$msg = "Erro: a pasta dessa filial não foi encontrada para exclusão.";
				$this->msgErro($msg);
				break;
			}
			
			case 30: {
				$msg = "Erro: não foi possível excluir a pasta da filial.";
				$this->msgErro($msg);
				break;
			}
			
			case 31: {
				$msg = "Status do arquivo alterado com sucesso.";
				$this->msgSucesso($msg);
				break;
			}
			
			case 32: {
				$msg = "Erro: não foi possível alterar o status do arquivo.";
				$this->msgErro($msg);
				break;
			}
			
			case 33: {
				$msg = "Erro: não foi possível excluir este arquivo do FTP.";
				$this->msgErro($msg);
				break;
			}
			
			case 34: {
				$msg = "Erro: este arquivo não existe, no caminho informado, no FTP.";
				$this->msgErro($msg);
				break;
			}
			
			case 35: {
				$msg = "Erro: o diretório desse arquivo não existe ou não foi encontrado no FTP.";
				$this->msgErro($msg);
				break;
			}
			
			case 36: {
				$msg = "Erro: ocorreu um problema ao fazer o envio desse arquivo.";
				$this->msgErro($msg);
				break;
			}
			
			case 37: {
				$msg = "Erro: ocorreu um problema ao fazer o envio da sua mensagem.";
				$this->msgErro($msg);
				break;
			}
			
			case 38: {
				$msg = "Sua mensagem foi enviada com sucesso.";
				$this->msgSucesso($msg);
				break;
			}
			
			case 39: {
				$msg = "Erro: houve um erro ao redimensionar a foto enviada.";
				$this->msgErro($msg);
				break;
			}
			
			case 40: {
				$msg = "Erro: houve um erro ao copiar/duplicar a foto enviada.";
				$this->msgErro($msg);
				break;
			}
			
			case 41: {
				$msg = "Erro: houve um erro ao criar a miniatura da foto enviada.";
				$this->msgErro($msg);
				break;
			}
   		}
	}
	
	/** MENSAGENS
	 * Mensagens que são apresentadas inline
	 * para o usuário nas operações CRUD.
	 **/
	 
	public function msgSucesso($mensagem) {
		echo "<div id='msgSucesso'><span></span>";
		echo $mensagem;
		echo "</div>";
	}
	
	public function msgErro($mensagem) {
		echo "<div id='msgErro'><span></span>";
		echo $mensagem;
		echo "</div>";
	}
	
	public function msgAlerta($mensagem) {
		echo "<div id='msgAlerta'><span></span>";
		echo $mensagem;
		echo "</div>";
	}
	
}
?>