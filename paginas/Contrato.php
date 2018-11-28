<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

if(empty($acaoPagina)) {
    $sqlDadosContrato = 'SELECT contrato.codigo,
                                date(contrato.validade) as data,
                                contrato.data_encerramento,
                                pessoa.nome as nome_pessoa
                           FROM contrato
                           JOIN pessoa
                             ON pessoa.codigo = contrato.codigo_pessoa';

    if(isUsuarioCliente()) {
        $sqlDadosContrato .= ' AND pessoa.codigo = '.$_SESSION['ID_USUARIO'];
    }

    $stmtDadosContrato = $conexao->prepare($sqlDadosContrato);
    $stmtDadosContrato->execute();

    $dados = array();
    while($linha = $stmtDadosContrato->fetch(PDO::FETCH_OBJ)) {
        $data = explode('-', $linha->data);
        $data = $data[2].'/'.$data[1].'/'.$data[0];

        $dados[] = array('codigo' => $linha->codigo,
                         'data' => $data,
                         'nome_pessoa' => $linha->nome_pessoa,
                         'data_encerramento' => $linha->data_encerramento);
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Contrato/Listar.php');
}
else if($acaoPagina == Constante::ACAO_INCLUIR) {
    require_once(Constante::DIRETORIO_ROOT.'/view/Contrato/Incluir.php');
}
else if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(!empty($idParametro)) {
        $sqlDadosContrato = 'SELECT contrato.codigo,
                                    pessoa.codigo as codigo_pessoa,
                                    pessoa.nome as nome_pessoa,
                                    contrato.codigo_plano,
                                    contrato.dia_primeira_parcela
                               FROM contrato
                               JOIN pessoa
                                 ON pessoa.codigo = contrato.codigo_pessoa
                              WHERE contrato.codigo = :codigo';
        $stmtDadosContrato = $conexao->prepare($sqlDadosContrato);
        $stmtDadosContrato->bindValue(':codigo', $idParametro);
        $stmtDadosContrato->execute();

        $dados = array();
        while($linha = $stmtDadosContrato->fetch(PDO::FETCH_OBJ)) {
            $sqlDadosPet = 'SELECT pet.codigo,
                                   pet.nome
                              FROM pet_contrato
                              JOIN pet
                                ON pet.codigo = pet_contrato.codigo_pet
                             WHERE pet_contrato.codigo_contrato = :codigo_contrato';
            $stmtDadosPet = $conexao->prepare($sqlDadosPet);
            $stmtDadosPet->bindValue(':codigo_contrato', $idParametro);
            $stmtDadosPet->execute();

            $dadosPet = [];
            while($linhaPet = $stmtDadosPet->fetch(PDO::FETCH_OBJ)) {
                $dadosPet[] = array('codigo' => $linhaPet->codigo,
                                    'nome' => $linhaPet->nome);
            }

            $dados = array('codigo' => $linha->codigo,
                           'codigo_pessoa' => $linha->codigo_pessoa,
                           'nome_pessoa' => $linha->nome_pessoa,
                           'codigo_plano' => $linha->codigo_plano,
                           'dia_primeira_parcela' => $linha->dia_primeira_parcela,
                           'dadosPet' => $dadosPet);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Contrato/Alterar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Contrato/Alterar.php');
    }
}
else if($acaoPagina == Constante::ACAO_EXCLUIR) {
    $sqlExcluirContrato = 'UPDATE contrato SET data_encerramento = :data_encerramento WHERE codigo = :codigo';
    $stmtExcluirContrato = $conexao->prepare($sqlExcluirContrato);
    $stmtExcluirContrato->bindValue(':codigo', $idParametro);
    $stmtExcluirContrato->bindValue(':data_encerramento', date('Y-m-d'));
    $stmtExcluirContrato->execute();

    $sqlExcluirFinanceiro = 'DELETE FROM parcela WHERE codigo_contrato = :codigo';
    $stmtExcluirFinanceiro = $conexao->prepare($sqlExcluirFinanceiro);
    $stmtExcluirFinanceiro->bindValue(':codigo', $idParametro);
    $stmtExcluirFinanceiro->execute();

    $_SESSION['MENSAGEM'] = 'Contrato Encerrado.';
    executaJs('location.href="'.Constante::LINK.'/contrato"');
    exit;
}
