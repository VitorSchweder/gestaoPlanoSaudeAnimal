<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');
require_once(Constante::DIRETORIO_ROOT.'/include/ConexaoPdo.php');

$retorno = array('erro' => null);

$conexao = ConexaoPdo::getConexao();

if(isset($_POST['codigoParcela'])) {
    $codigoParcela = filter_var($_POST['codigoParcela'], FILTER_SANITIZE_NUMBER_INT);
    $acao = filter_var($_POST['acao'], FILTER_SANITIZE_STRING);

    if($acao == 'baixa') {
        $sqlBaixaParcela = 'UPDATE parcela SET situacao = 0 WHERE codigo = :codigo';
        $stmtBaixaParcela = $conexao->prepare($sqlBaixaParcela);
        $stmtBaixaParcela->bindValue(':codigo', $codigoParcela);
        $stmtBaixaParcela->execute();
    }
    else if($acao == 'estorno') {
        $sqlEstornaParcela = 'UPDATE parcela SET situacao = 1 WHERE codigo = :codigo';
        $stmtEstornaParcela = $conexao->prepare($sqlEstornaParcela);
        $stmtEstornaParcela->bindValue(':codigo', $codigoParcela);
        $stmtEstornaParcela->execute();
    }
}

echo json_encode($retorno);
