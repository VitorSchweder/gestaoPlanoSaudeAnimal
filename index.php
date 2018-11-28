<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once('include/Constante.php');
require_once('include/ConexaoPdo.php');
require_once('include/Funcoes.php');

$conexao = ConexaoPdo::getConexao();

$paginaIncluir = 'home';
$paginasPermitidas = ['login', 'home', 'pessoa', 'procedimento', 'plano', 'pet', 'consulta', 'configuracoes', 'validaLogin', 'logout', 'alterarSenha', 'contrato', 'exibeContrato', 'exibeEtiqueta', 'financeiro'];
$acoesPermitidas = ['incluir', 'alterar', 'excluir', 'visualizar'];

require_once(Constante::DIRETORIO_ROOT.'/Cabecalho.php');

if(isset($_GET['url'])) {
    $url = filter_var($_GET['url'], FILTER_SANITIZE_STRING);

    $urlAux = explode('/', $url);

    $idParametro = null;
    $acaoPagina = null;

    if(count($urlAux) <= 3) {
        if(isset($urlAux[0])) {
            $paginaIncluir = filter_var(str_replace('/','',$urlAux[0]), FILTER_SANITIZE_STRING);

            if(isset($urlAux[1]) && !empty($urlAux[1])) {
                $acaoPagina = filter_var(str_replace('/','',$urlAux[1]), FILTER_SANITIZE_STRING);
            }

            if(in_array($paginaIncluir, $paginasPermitidas) && !isset($acaoPagina)
            || (in_array($acaoPagina, $acoesPermitidas) && in_array($paginaIncluir, $paginasPermitidas))) {
                if(isset($urlAux[1])) {
                    $acaoPagina = filter_var(str_replace('/','',$urlAux[1]), FILTER_SANITIZE_STRING);
                }

                if(isset($urlAux[2])) {
                    $idParametro = filter_var(str_replace('/','',$urlAux[2]), FILTER_SANITIZE_NUMBER_INT);
                }

                include(Constante::DIRETORIO_ROOT.'/paginas/'.ucfirst($paginaIncluir).'.php');
            }
            else {
                if(substr_count($paginaIncluir,'pdfEtiqueta') > 0) {
                    include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
                }
            }
        }
        else {
            include(Constante::DIRETORIO_ROOT.'/paginas/Home.php');
        }
    }
    else {
        include(Constante::DIRETORIO_ROOT.'/paginas/Home.php');
    }
}
else {
    include(Constante::DIRETORIO_ROOT.'/paginas/Home.php');
}

require_once(Constante::DIRETORIO_ROOT.'/Rodape.php');
