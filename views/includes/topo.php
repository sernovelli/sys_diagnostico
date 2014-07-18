<header>
    <div class="boasvindas">
		<?php
			echo "Olá ".$_SESSION['nomeUsuario']." (".$_SESSION['idCliente']."). Você está logado em ".$_SESSION['filial']." (".$_SESSION['idFilial'].")";
			//echo "Você está logado como ".$_SESSION['nomePerfil'].".";
		?>
    </div>
    <nav>
        <ul>
            <li class="config">Configurações<span></span>
            <?php if ($_SESSION['hashid'] == "cliente") { ?>
                <ul>
                    <li><a href="?controle=DadosAcesso&acao=manter&id=<?php echo $_SESSION['idUsuario']; ?>">Meus dados de acesso</a></li>
                    <li><a href="?controle=Cliente&acao=manter&id=<?php echo $_SESSION['idCliente']; ?>">Meu cadastro</a></li>
                    <li><a href="?controle=Suporte&acao=manter&idFilial=<?php echo $_SESSION['idFilial']; ?>">Suporte da filial</a></li>
                </ul>
            <?php } else if ($_SESSION['hashid'] == "gerentefilial") { ?>
                <ul>
                    <li><a href="?controle=DadosAcesso&acao=manter&id=<?php echo $_SESSION['idUsuario']; ?>">Meus dados de acesso</a></li>
                    <li><a href="?controle=Filial&acao=manter&id=<?php echo $_SESSION['idFilial']; ?>">Minha empresa/filial</a></li>
                    <li><a href="?controle=Suporte&acao=manter&idFilial=<?php echo $_SESSION['idFilial']; ?>">Suporte da Matriz</a></li>
                </ul>
            <?php } else if ($_SESSION['hashid'] == "superadmin") { ?>
            	<ul>
                    <li><a href="?controle=DadosAcesso&acao=manter&id=<?php echo $_SESSION['idUsuario']; ?>">Dados de acesso</a></li>
                    <li><a href="?controle=Suporte&acao=manter">Suporte T&eacute;cnico</a></li>
                </ul>
            <?php } ?>
            </li>
            
        </ul>
    </nav>
    <a id="sair" href="?controle=Sair&acao=encerraSessao">Sair</a>
</header>