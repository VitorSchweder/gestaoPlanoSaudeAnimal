<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(empty($acaoPagina)) {
    $sqlDadosPlano = 'SELECT codigo,
                             nome,
                             valor,
                             porcentagem_procedimento
                        FROM plano';
    $stmtDadosPlano = $conexao->prepare($sqlDadosPlano);
    $stmtDadosPlano->execute();

    $dados = array();
    while($linha = $stmtDadosPlano->fetch(PDO::FETCH_OBJ)) {
        $dados[] = array('codigo' => $linha->codigo,
                         'nome' => $linha->nome,
                         'valor' => $linha->valor,
                         'porcentagem_procedimento' => $linha->porcentagem_procedimento);
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Plano/Listar.php');
}
else if($acaoPagina == Constante::ACAO_INCLUIR) {
    $sqlDadosProcedimento = 'SELECT codigo,
                                    nome,
                                    valor,
                                    tipo,
                                    case when peso = 0
                                        then \'\'
                                        when peso = 1
                                            then \'(Até 5kg)\'
                                        when peso = 2
                                            then \'(De 5 a 10kg)\'
                                        when peso = 3
                                            then \'(De 10 a 20kg)\'
                                        when peso = 4
                                            then \'(Acima de 20kg)\'
                                    end as peso
                               FROM procedimento';
    $stmtDadosProcedimento = $conexao->prepare($sqlDadosProcedimento);
    $stmtDadosProcedimento->execute();

    $dados = array();
    while($linha = $stmtDadosProcedimento->fetch(PDO::FETCH_OBJ)) {
        $dados[] = array('codigo' => $linha->codigo,
                         'nome' => $linha->nome,
                         'valor' => $linha->valor,
                         'valor_original' => $linha->valor,
                         'tipo' => $linha->tipo,
                         'peso' => $linha->peso);
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Plano/Incluir.php');
}
else if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(!empty($idParametro)) {
        $sqlDadosPlano = 'SELECT plano.codigo,
                                 plano.nome,
                                 plano.valor,
                                 plano.porcentagem_procedimento,
                                 plano.limite_pet
                            FROM plano
                           WHERE plano.codigo = :codigo';
        $stmtDadosPlano = $conexao->prepare($sqlDadosPlano);
        $stmtDadosPlano->bindValue(':codigo', $idParametro);
        $stmtDadosPlano->execute();

        $dados = array();
        while($linha = $stmtDadosPlano->fetch(PDO::FETCH_OBJ)) {
            $sqlProcedimento = 'SELECT procedimento.codigo as codigo_procedimento,
                                       procedimento.nome as nome_procedimento,
                                       procedimento.tipo as tipo_desconto,
                                       procedimento.valor as valor_original,
                                       procedimento_plano.valor_procedimento,
                                       case when peso = 0
                                        then \'\'
                                        when peso = 1
                                            then \'(Até 5kg)\'
                                        when peso = 2
                                            then \'(De 5 a 10kg)\'
                                        when peso = 3
                                            then \'(De 10 a 20kg)\'
                                        when peso = 4
                                            then \'(Acima de 20kg)\'
                                    end as peso

                                  FROM procedimento_plano
                                  JOIN procedimento
                                    ON procedimento.codigo = procedimento_plano.codigo_procedimento
                                 WHERE procedimento_plano.codigo_plano = :codigo_plano';
            $stmtProcedimento = $conexao->prepare($sqlProcedimento);
            $stmtProcedimento->bindValue(':codigo_plano', $idParametro);
            $stmtProcedimento->execute();

            $dadosProcedimento = array();
            while($linhaProcedimento = $stmtProcedimento->fetch(PDO::FETCH_OBJ)) {
                $dadosProcedimento[] = array('codigo' => $linhaProcedimento->codigo_procedimento,
                                             'nome' => $linhaProcedimento->nome_procedimento,
                                             'valor' => $linhaProcedimento->valor_procedimento,
                                             'valor_original' => $linhaProcedimento->valor_original,
                                             'tipo' => $linhaProcedimento->tipo_desconto,
                                             'peso' => $linhaProcedimento->peso);
            }

            $dados = array('codigo' => $linha->codigo,
                           'nome' => $linha->nome,
                           'valor' => $linha->valor,
                           'limite_pet' => $linha->limite_pet,
                           'porcentagem_procedimento' => $linha->porcentagem_procedimento,
                           'dadosProcedimentos' => $dadosProcedimento);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Plano/Alterar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Plano/Alterar.php');
    }
}
else if($acaoPagina == Constante::ACAO_EXCLUIR) {
    $sqlExcluirPlano = 'DELETE FROM plano WHERE codigo = :codigo';
    $stmtExcluirPlano = $conexao->prepare($sqlExcluirPlano);
    $stmtExcluirPlano->bindValue(':codigo', $idParametro);
    $stmtExcluirPlano->execute();

    $sqlExcluirProcedimentoPlano = 'DELETE FROM procedimento_plano WHERE codigo_plano = :codigo_plano';
    $stmtExcluirProcedimentoPlano = $conexao->prepare($sqlExcluirProcedimentoPlano);
    $stmtExcluirProcedimentoPlano->bindValue(':codigo_plano', $idParametro);
    $stmtExcluirProcedimentoPlano->execute();

    $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_EXCLUIDO;
    executaJs('location.href="'.Constante::LINK.'/plano"');
    exit;
}
