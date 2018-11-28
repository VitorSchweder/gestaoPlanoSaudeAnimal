<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

if(empty($acaoPagina)) {
    $sqlDadosConsulta = 'SELECT SUM(procedimento_plano.valor_procedimento) as valor,
                                consulta.codigo,
                                date(consulta.data) as data,
                                pessoa.nome as nome_pessoa,
                                pet.nome as nome_pet
                           FROM consulta
                           JOIN pessoa
                             ON pessoa.codigo = consulta.codigo_pessoa
                           JOIN pet
                             ON pet.codigo = consulta.codigo_pet
                           JOIN contrato
                             ON contrato.codigo_pessoa = pessoa.codigo
                           JOIN procedimento_consulta
                             ON procedimento_consulta.codigo_consulta = consulta.codigo
                           JOIN procedimento_plano
                             ON procedimento_plano.codigo_plano = contrato.codigo_plano
                            AND procedimento_plano.codigo_procedimento = procedimento_consulta.codigo_procedimento';

    if(isUsuarioCliente()) {
        $sqlDadosConsulta .= ' AND pessoa.codigo = '.$_SESSION['ID_USUARIO'];
    }

    $sqlDadosConsulta .= '
                       GROUP BY consulta.codigo,
                                data,
                                nome_pessoa,
                                nome_pet';

    $stmtDadosConsulta = $conexao->prepare($sqlDadosConsulta);
    $stmtDadosConsulta->execute();

    $dados = array();
    while($linha = $stmtDadosConsulta->fetch(PDO::FETCH_OBJ)) {
        $data = explode('-', $linha->data);
        $data = $data[2].'/'.$data[1].'/'.$data[0];

        $dados[] = array('codigo' => $linha->codigo,
                         'data' => $data,
                         'nome_pessoa' => $linha->nome_pessoa,
                         'nome_pet' => $linha->nome_pet,
                         'valor' => 'R$ '.number_format($linha->valor, 2,',','.'));
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Consulta/Listar.php');
}
else if($acaoPagina == Constante::ACAO_INCLUIR) {
    require_once(Constante::DIRETORIO_ROOT.'/view/Consulta/Incluir.php');
}
else if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(!empty($idParametro)) {
        $sqlDadosConsulta = 'SELECT consulta.codigo,
                                    consulta.observacoes,
                                    pessoa.codigo as codigo_pessoa,
                                    pessoa.nome as nome_pessoa,
                                    pet.codigo as codigo_pet,
                                    pet.nome as nome_pet
                               FROM consulta
                               JOIN pessoa
                                 ON pessoa.codigo = consulta.codigo_pessoa
                               JOIN pet
                                 ON pet.codigo = consulta.codigo_pet
                              WHERE consulta.codigo = :codigo';
        $stmtDadosConsulta = $conexao->prepare($sqlDadosConsulta);
        $stmtDadosConsulta->bindValue(':codigo', $idParametro);
        $stmtDadosConsulta->execute();

        $dados = array();
        while($linha = $stmtDadosConsulta->fetch(PDO::FETCH_OBJ)) {
            $sqlDadosProcedimentos = 'SELECT codigo,
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
                                             end as peso
                                        FROM procedimento_consulta
                                        JOIN procedimento
                                          ON procedimento.codigo = procedimento_consulta.codigo_procedimento
                                       WHERE procedimento_consulta.codigo_consulta = :codigo_consulta';
            $stmtDadosProcedimentos = $conexao->prepare($sqlDadosProcedimentos);
            $stmtDadosProcedimentos->bindValue(':codigo_consulta', $idParametro);
            $stmtDadosProcedimentos->execute();

            $dadosProcedimentos = [];
            while($linhaProcedimento = $stmtDadosProcedimentos->fetch(PDO::FETCH_OBJ)) {
                $dadosProcedimentos[] = array('codigo' => $linhaProcedimento->codigo,
                                              'nome' => $linhaProcedimento->nome,
                                              'peso' => $linhaProcedimento->peso);
            }

            $dados = array('codigo' => $linha->codigo,
                           'observacoes' => $linha->observacoes,
                           'codigo_pessoa' => $linha->codigo_pessoa,
                           'nome_pessoa' => $linha->nome_pessoa,
                           'codigo_pet' => $linha->codigo_pet,
                           'nome_pet' => $linha->nome_pet,
                           'dadosProcedimentos' => $dadosProcedimentos);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Consulta/Alterar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Consulta/Alterar.php');
    }
}
else if($acaoPagina == Constante::ACAO_VISUALIZAR) {
    if(!empty($idParametro)) {
        $sqlDadosConsulta = 'SELECT consulta.codigo,
                                    consulta.observacoes,
                                    pessoa.codigo as codigo_pessoa,
                                    pessoa.nome as nome_pessoa,
                                    pet.codigo as codigo_pet,
                                    pet.nome as nome_pet
                               FROM consulta
                               JOIN pessoa
                                 ON pessoa.codigo = consulta.codigo_pessoa
                               JOIN pet
                                 ON pet.codigo = consulta.codigo_pet
                              WHERE consulta.codigo = :codigo';
        $stmtDadosConsulta = $conexao->prepare($sqlDadosConsulta);
        $stmtDadosConsulta->bindValue(':codigo', $idParametro);
        $stmtDadosConsulta->execute();

        $dados = array();
        while($linha = $stmtDadosConsulta->fetch(PDO::FETCH_OBJ)) {
            $sqlDadosProcedimentos = 'SELECT codigo,
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
                                             end as peso
                                        FROM procedimento_consulta
                                        JOIN procedimento
                                          ON procedimento.codigo = procedimento_consulta.codigo_procedimento
                                       WHERE procedimento_consulta.codigo_consulta = :codigo_consulta';
            $stmtDadosProcedimentos = $conexao->prepare($sqlDadosProcedimentos);
            $stmtDadosProcedimentos->bindValue(':codigo_consulta', $idParametro);
            $stmtDadosProcedimentos->execute();

            $dadosProcedimentos = [];
            while($linhaProcedimento = $stmtDadosProcedimentos->fetch(PDO::FETCH_OBJ)) {
                $dadosProcedimentos[] = array('codigo' => $linhaProcedimento->codigo,
                                              'nome' => $linhaProcedimento->nome,
                                              'peso' => $linhaProcedimento->peso);
            }

            $dados = array('codigo' => $linha->codigo,
                           'observacoes' => $linha->observacoes,
                           'codigo_pessoa' => $linha->codigo_pessoa,
                           'nome_pessoa' => $linha->nome_pessoa,
                           'codigo_pet' => $linha->codigo_pet,
                           'nome_pet' => $linha->nome_pet,
                           'dadosProcedimentos' => $dadosProcedimentos);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Consulta/Visualizar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Consulta/Visualizar.php');
    }
}
else if($acaoPagina == Constante::ACAO_EXCLUIR) {
    $sqlExcluirConsulta = 'DELETE FROM consulta WHERE codigo = :codigo';
    $stmtExcluirConsulta = $conexao->prepare($sqlExcluirConsulta);
    $stmtExcluirConsulta->bindValue(':codigo', $idParametro);
    $stmtExcluirConsulta->execute();

    $sqlExcluirProcedimentoConsulta = 'DELETE FROM procedimento_consulta WHERE codigo_consulta = :codigo_consulta';
    $stmtExcluirProcedimentoConsulta = $conexao->prepare($sqlExcluirProcedimentoConsulta);
    $stmtExcluirProcedimentoConsulta->bindValue(':codigo_consulta', $idParametro);
    $stmtExcluirProcedimentoConsulta->execute();

    $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_EXCLUIDO;
    executaJs('location.href="'.Constante::LINK.'/consulta"');
    exit;
}
