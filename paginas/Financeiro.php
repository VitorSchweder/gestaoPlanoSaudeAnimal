<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

if(empty($acaoPagina)) {
    $sqlDadosContrato = 'SELECT contrato.codigo,
                                pessoa.nome as nome_pessoa,
                                (SELECT parcela.data_parcela
                                   FROM parcela
                                  WHERE codigo_contrato = contrato.codigo
                                    AND situacao = 1
                               ORDER BY data_parcela limit 1) as data_parcela
                           FROM contrato
                           JOIN pessoa
                             ON pessoa.codigo = contrato.codigo_pessoa
                          WHERE contrato.codigo IN(SELECT parcela.codigo_contrato FROM parcela WHERE parcela.codigo_contrato = contrato.codigo)';

    if(isUsuarioCliente()) {
        $sqlDadosContrato .= ' AND pessoa.codigo = '.$_SESSION['ID_USUARIO'];
    }

    $stmtDadosContrato = $conexao->prepare($sqlDadosContrato);
    $stmtDadosContrato->execute();

    $dataAtual = date('Y-m-d');

    $dados = array();
    while($linha = $stmtDadosContrato->fetch(PDO::FETCH_OBJ)) {
        $dataParcela = $linha->data_parcela;

        $situacao = 'Em dia';
        if (!empty($dataParcela) && $dataAtual > $dataParcela) {
            $situacao = 'Em atraso';
        }

        $dados[] = array('codigo_contrato' => $linha->codigo,
                         'nome_pessoa' => $linha->nome_pessoa,
                         'situacao' => $situacao);
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Financeiro/Listar.php');
}
else if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(!empty($idParametro)) {
        $sqlDadosParcela = 'SELECT parcela.codigo,
                                   parcela.numero_parcela,
                                   parcela.data_parcela,
                                   parcela.valor_parcela,
                                   parcela.situacao as situacao_parcela,
                                   pessoa.nome as nome_pessoa
                              FROM parcela
                              JOIN contrato
                                ON contrato.codigo = parcela.codigo_contrato
                              JOIN pessoa
                                ON pessoa.codigo = contrato.codigo_pessoa
                             WHERE parcela.codigo_contrato = :codigo_contrato';
        $stmtDadosParcela = $conexao->prepare($sqlDadosParcela);
        $stmtDadosParcela->bindValue(':codigo_contrato', $idParametro);
        $stmtDadosParcela->execute();

        $dados = array();
        while($linha = $stmtDadosParcela->fetch(PDO::FETCH_OBJ)) {
            $dados[] = array('codigo' => $linha->codigo,
                             'nome_pessoa' => $linha->nome_pessoa,
                             'numero_parcela' => $linha->numero_parcela,
                             'data_parcela' => $linha->data_parcela,
                             'valor_parcela' => $linha->valor_parcela,
                             'situacao_parcela' => $linha->situacao_parcela);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Financeiro/Alterar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Financeiro/Alterar.php');
    }
}
