<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(empty($acaoPagina)) {
    $sqlDadosPessoa = 'SELECT codigo,
                              nome,
                              cpf,
                              email,
                              logradouro,
                              numero,
                              bairro,
                              email,
                              case when tipo = 1
                                  then \'Administrador\'
                                  when tipo = 2
                                      then \'Veterinário\'
                                  when tipo = 3
                                      then \'Cliente\'
                              end as tipo
                         FROM pessoa';

    $stmtDadosPessoa = $conexao->prepare($sqlDadosPessoa);
    $stmtDadosPessoa->execute();

    $dados = array();
    while($linha = $stmtDadosPessoa->fetch(PDO::FETCH_OBJ)) {
        $numero = $linha->numero;
        $endereco = $linha->logradouro;

        if(!empty($numero)) {
            $endereco .= ', '.$numero;
        }

        $endereco .= ', '.$linha->bairro;

        $dados[] = array('codigo' => $linha->codigo,
                         'nome' => $linha->nome,
                         'cpf' => $linha->cpf,
                         'email' => $linha->email,
                         'endereco' => $endereco,
                         'tipo' => $linha->tipo);
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Pessoa/Listar.php');
}
else if($acaoPagina == Constante::ACAO_INCLUIR) {
    require_once(Constante::DIRETORIO_ROOT.'/view/Pessoa/Incluir.php');
}
else if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(!empty($idParametro)) {
        $sqlDadosPessoa = 'SELECT codigo,
                                  nome,
                                  cpf,
                                  email,
                                  logradouro,
                                  numero,
                                  complemento,
                                  bairro,
                                  email,
                                  cep,
                                  cidade,
                                  estado,
                                  tipo,
                                  possui_acesso
                             FROM pessoa
                            WHERE codigo = :codigo';

        $stmtDadosPessoa = $conexao->prepare($sqlDadosPessoa);
        $stmtDadosPessoa->bindValue(':codigo', $idParametro);
        $stmtDadosPessoa->execute();

        $numeroTelefone = array();
        $dados = array();
        while($linha = $stmtDadosPessoa->fetch(PDO::FETCH_OBJ)) {
            $sqlTelefone = 'SELECT numero FROM telefone WHERE codigo_pessoa = :codigo_pessoa';
            $stmtTelefone = $conexao->prepare($sqlTelefone);
            $stmtTelefone->bindValue(':codigo_pessoa', $linha->codigo);
            $stmtTelefone->execute();

            while($linhaTelefone = $stmtTelefone->fetch(PDO::FETCH_OBJ)) {
                $numeroTelefone[] = $linhaTelefone->numero;
            }

            $dados = array('codigo' => $linha->codigo,
                           'nome' => $linha->nome,
                           'cpf' => $linha->cpf,
                           'email' => $linha->email,
                           'logradouro' => $linha->logradouro,
                           'numero' => $linha->numero,
                           'complemento' => $linha->complemento,
                           'bairro' => $linha->bairro,
                           'cep' => $linha->cep,
                           'cidade' => $linha->cidade,
                           'estado' => $linha->estado,
                           'tipo' => $linha->tipo,
                           'telefone' => $numeroTelefone,
                           'possui_acesso' => $linha->possui_acesso);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Pessoa/Alterar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Pessoa/Alterar.php');
    }
}
else if($acaoPagina == Constante::ACAO_EXCLUIR) {
    $sqlExcluirPessoa = 'DELETE FROM pessoa WHERE codigo = :codigo';
    $stmtExcluirPessoa = $conexao->prepare($sqlExcluirPessoa);
    $stmtExcluirPessoa->bindValue(':codigo', $idParametro);
    $stmtExcluirPessoa->execute();

    $sqlExcluirTelefone = 'DELETE FROM telefone WHERE codigo_pessoa = :codigo_pessoa';
    $stmtExcluirTelefone = $conexao->prepare($sqlExcluirTelefone);
    $stmtExcluirTelefone->bindValue(':codigo_pessoa', $idParametro);
    $stmtExcluirTelefone->execute();

    $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_EXCLUIDO;
    executaJs('location.href="'.Constante::LINK.'/pessoa"');
    exit;
}
