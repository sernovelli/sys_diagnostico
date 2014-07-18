<?php 
//$vetorMenu = array($_REQUEST['menu']);
include_once "controllers/MnVertical.php";
$menu = new Mnvertical();
//$vetorMenu = $menu->listarAction($_SESSION['idPerfilUsuario']);
$vetorMenu = $menu->listarAction($_SESSION['hashid']);


?>

<aside>
    <a id="logo" href="?controle=Inicio&acao=dashboard" title="Diagnostic - Gerenciador de Arquivos">Diagnostic</a>
    <nav>
        <?php foreach($vetorMenu AS $itemMenu) {
            if ($itemMenu->getMenuPai() == 0) { ?>
                <h3><a href="<?php echo $itemMenu->getUrl() ?>"><?php echo $itemMenu->getMenu() ?></a></h3>
        <?php } else { ?>
                <a href="<?php echo $itemMenu->getUrl() ?>">&bull; <?php echo $itemMenu->getMenu() ?></a>
        <?php } } ?>
    </nav>
    <a id="akt" href="http://www.alkantara.com.br/" target="_blank" title="Alkantára">Desenvolvido por Alkantára</a>
</aside>

