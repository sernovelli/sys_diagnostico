<?php
/*
    PaginacaoClass - Classe PHP de Montagem de Navegadores de Página
    ----------------------------------------------------------------
    Autor: Fernando Val <hide@address.com>

    Descrição:
        PaginacaoClass é uma classe em PHP para fazer os links de navegação de página no formato
        "Anterior <primeira> ... <n-3> <n-2> <n-1> N <n+1> <n+2> <n+3> ... <ultima> Próxima".

        A classe é totalmente personalizável, permitindo que o programador defina os seguintes
        parâmetros:

        - Link do site;
        - Variável GET de definição da página;
        - Número de páginas navegáveis laterais a página atual;
        - Texto usado para o separador da primeira e últiam páginas dos navegadores de páginas;
        - Texto da link "Anterior";
        - Texto do link "Próxima";
        - Classes HTML dos links, página atual e separadores.

    Licença:
        Esta classe é Open Source e distribuida sob a licença GPL.

    Histórico:
        1.00 - 21/Mar/2007 - Primeira versão da classe
*/

class Paginacao {
    // Propriedades da classe
    var $PaginaAtual = 1;       // Define a página atual
    var $UltimaPagina = 1;      // Define a quantidade de páginas
    var $NumPgLaterais = 3;     // Define quantas páginas serão navegáveis para os lados a partir da página atual
    var $SiteLink = '?controle=Foto&acao=listar'; // Define o hyperlink dos navegadores
    var $PageGET = 'pag';       // Define a variável do GET que receberá o número da página para navegar

    var $HTMLPaginacao = '';    // Código HTML com a navegação

    var $TextoSeparador = '...';                    // Texto a ser mostrado como separador para primeira e última páginas
    var $ClassePaginaAtual = 'paginacao_atual';     // Classe CSS usada para a LABEL de página atual
    var $ClasseNavegadores = 'paginacao_navegar';   // Classe CSS usada para os LINKs de navegação
    var $ClasseSeparadores = 'paginacao_atual';     // Classe CSS usada para os LABELS dos separadores de primeira e última páginas

    var $TextoAnterior = 'Anterior';                // Texto a ser mostrado no link para a página anterior
    var $TextoProxima = 'Pr&oacute;xima';           // Texto a ser mostrado no link para a príxima página

    function MontarPaginacao($pgatual = 0, $pgfim = 0) {
        // Verifica se a página atual e/ou a última foram passadas por parâmetro na chamada
        if ($pgatual)
            $this->PaginaAtual = $pgatual;
        if ($pgfim)
            $this->UltimaPagina = $pgfim;

        // Monta o link
        if (strpos($this->SiteLink, '?') === FALSE) {
            $link = $this->SiteLink . '?' . $this->PageGET . '=';
        } else {
            $link = $this->SiteLink . '&' . $this->PageGET . '=';
        }

        // Verifica se tem navagação pra página anterior
        $anterior = '';
        if ($this->PaginaAtual > 1) {
            $anterior = '<A HREF="'.$link.($this->PaginaAtual - 1).'" CLASS="'.$this->ClasseNavegadores.'">'.$this->TextoAnterior.'</A> ';
        }

        // Verifica se mostra navegador para primeira página
        $primeira = '';
        if (($this->PaginaAtual - ($this->NumPgLaterais + 1) > 1) && ($this->UltimaPagina > ($this->NumPgLaterais * 2 + 2))) {
            $primeira = '<A HREF="'.$link.'1" CLASS="'.$this->ClasseNavegadores.'">1</A> <LABEL CLASS="'.$this->ClasseSeparadores.'">'.$this->TextoSeparador.'</LABEL> ';
            $dec = $this->NumPgLaterais;
        } else {
            $dec = $this->PaginaAtual;
            while ($this->PaginaAtual - $dec < 1) {
                $dec--;
            }
        }

        // Verifica se mostra navegador para última página
        $ultima = '';
        if (($this->PaginaAtual + ($this->NumPgLaterais + 1) < $this->UltimaPagina) && ($this->UltimaPagina > ($this->NumPgLaterais * 2 + 2))) {
            $ultima = '<LABEL CLASS="'.$this->ClasseSeparadores.'">'.$this->TextoSeparador.'</LABEL> <A HREF="'.$link.$this->UltimaPagina.'" CLASS="'.$this->ClasseNavegadores.'">'.$this->UltimaPagina.'</A>';
            $inc = $this->NumPgLaterais;
        } else {
            $inc = $this->UltimaPagina - $this->PaginaAtual;
        }

        // Se houverem menos páginas anteriores que o definido, tenta colocar mais páginas para a frente
        if ($dec < $this->NumPgLaterais) {
            $x = $this->NumPgLaterais - $dec;
            while ($this->PaginaAtual + $inc < $this->UltimaPagina && $x > 0) {
                $inc++;
                $x--;
            }
        }
        // Se houverem menos páginas seguintes que o definido, tenta colocar mais páginas para trás
        if ($inc < $this->NumPgLaterais) {
            $x = $this->NumPgLaterais - $inc;
            while ($this->PaginaAtual - $dec > 1 && $x > 0) {
                $dec++;
                $x--;
            }
        }

        // Monta o conteúdo central do navegador
        $atual = '';
        for ($x = $this->PaginaAtual - $dec; $x <= $this->PaginaAtual + $inc; $x++) {
            if ($x == $this->PaginaAtual) {
                $atual .= '<LABEL CLASS="'.$this->ClassePaginaAtual.'">'.$x.'</LABEL> ';
            } else {
                $atual .= '<A HREF="'.$link.$x.'" CLASS="'.$this->ClasseNavegadores.'">'.$x.'</A> ';
            }
        }

        // Verifica se mostra navegador para próxima página
        $proxima = '';
        if ($this->PaginaAtual < $this->UltimaPagina) {
            $proxima = ' <A HREF="'.$link.($this->PaginaAtual + 1).'" CLASS="'.$this->ClasseNavegadores.'">'.$this->TextoProxima.'</A>';
        }

        return $this->HTMLPaginacao = $anterior.$primeira.$atual.$ultima.$proxima;
    }
}


/*
    Exemplo de código PHP de como usar esta classe
    ----------------------------------------------

    include('PaginacaoClass.php');
    $Paginacao = new Paginacao;
    $pag_atual = empty($_GET['pag']) ? 1 : (int)$_GET['pag'];
    $sql = 'SELECT COUNT(1) FROM minhatabela';
    $res = mysql_query($sql);
    $reg = mysql_fetch_row($res);
    $num_reg_lin = 10;
    $ultima_pag = ceil((int)$reg[0] / $num_reg_lin);
    // imprime os registros
    print $Paginacao->MontarPaginacao($pag_atual, $ultima_pag);
*/
?>