<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(empty($acaoPagina)) {
    $sqlDadosProcedimento = 'SELECT codigo,
                                    nome,
                                    case when peso = 0
                                        then \'Não informado\'
                                        when peso = 1
                                            then \'Até 5kg\'
                                        when peso = 2
                                            then \'De 5 a 10kg\'
                                        when peso = 3
                                            then \'De 10 a 20kg\'
                                        when peso = 4
                                            then \'Acima de 20kg\'
                                    end as peso,
                                    valor,
                                    tipo
                              FROM procedimento';
    $stmtDadosProcedimento = $conexao->prepare($sqlDadosProcedimento);
    $stmtDadosProcedimento->execute();

    $dados = array();
    while($linha = $stmtDadosProcedimento->fetch(PDO::FETCH_OBJ)) {
        $dados[] = array('codigo' => $linha->codigo,
                         'nome' => $linha->nome,
                         'peso' => $linha->peso,
                         'valor' => $linha->valor,
                         'tipo' => $linha->tipo);
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Procedimento/Listar.php');
}
else if($acaoPagina == Constante::ACAO_INCLUIR) {
    require_once(Constante::DIRETORIO_ROOT.'/view/Procedimento/Incluir.php');
}
else if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(!empty($idParametro)) {
        $sqlDadosProcedimento = 'SELECT codigo,
                                        nome,
                                        peso,
                                        valor,
                                        tipo
                                   FROM procedimento
                                  WHERE codigo = :codigo';
        $stmtDadosProcedimento = $conexao->prepare($sqlDadosProcedimento);
        $stmtDadosProcedimento->bindValue(':codigo', $idParametro);
        $stmtDadosProcedimento->execute();

        $dados = array();
        while($linha = $stmtDadosProcedimento->fetch(PDO::FETCH_OBJ)) {
            $dados = array('codigo' => $linha->codigo,
                           'nome' => $linha->nome,
                           'peso' => $linha->peso,
                           'valor' => $linha->valor,
                           'tipo' => $linha->tipo);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Procedimento/Alterar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Procedimento/Alterar.php');
    }
}
else if($acaoPagina == Constante::ACAO_EXCLUIR) {
    $sqlExcluirProcedimento = 'DELETE FROM procedimento WHERE codigo = :codigo';
    $stmtExcluirProcedimento = $conexao->prepare($sqlExcluirProcedimento);
    $stmtExcluirProcedimento->bindValue(':codigo', $idParametro);
    $stmtExcluirProcedimento->execute();

    $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_EXCLUIDO;
    executaJs('location.href="'.Constante::LINK.'/procedimento"');
    exit;
}
