<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');
require_once(Constante::DIRETORIO_ROOT.'/include/ConexaoPdo.php');

$conexao = ConexaoPdo::getConexao();

if(isset($_GET['term'])) {
    $nome = filter_var($_GET['term'], FILTER_SANITIZE_STRING);
    $rel = filter_var($_GET['rel'], FILTER_SANITIZE_STRING);

    $sqlPessoa = 'SELECT codigo,
                         nome
                    FROM pessoa
                   WHERE nome like \'%'.$nome.'%\'';

    if($rel == 'consulta') {
        $sqlPessoa .= ' AND pessoa.codigo in (SELECT pessoa.codigo
                                                FROM contrato
                                                JOIN pessoa
                                                  ON pessoa.codigo = contrato.codigo_pessoa
                                               WHERE nome like \'%'.$nome.'%\')';
    }

    $stmtPessoa = $conexao->prepare($sqlPessoa);
    $stmtPessoa->execute();

    $htmlLista = null;
    while($linha = $stmtPessoa->fetch(PDO::FETCH_OBJ)) {
        $htmlLista[] = array('id' => $linha->codigo, 'label' => $linha->nome, 'value' => $linha->nome);
    }

    echo json_encode($htmlLista);
}
