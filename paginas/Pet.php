<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(empty($acaoPagina)) {
    $sqlDadosPet = 'SELECT pet.codigo,
                           pet.nome,
                           pet.raca,
                           pessoa.nome as nome_pessoa
                      FROM pet
                      JOIN pessoa
                        ON pessoa.codigo = pet.codigo_pessoa';
    $stmtDadosPet = $conexao->prepare($sqlDadosPet);
    $stmtDadosPet->execute();

    $dados = array();
    while($linha = $stmtDadosPet->fetch(PDO::FETCH_OBJ)) {
        $dados[] = array('codigo' => $linha->codigo,
                         'nome' => $linha->nome,
                         'raca' => $linha->raca,
                         'nome_pessoa' => $linha->nome_pessoa);
    }

    require_once(Constante::DIRETORIO_ROOT.'/view/Pet/Listar.php');
}
else if($acaoPagina == Constante::ACAO_INCLUIR) {
    require_once(Constante::DIRETORIO_ROOT.'/view/Pet/Incluir.php');
}
else if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(!empty($idParametro)) {
        $sqlDadosPet = 'SELECT pet.codigo,
                               pet.nome,
                               pet.raca,
                               pet.especie,
                               pet.caracteristica,
                               pet.idade,
                               pet.tipo_idade,
                               pet.peso,
                               pet.sexo,
                               pet.pelagem,
                               pet.vacina,
                               pessoa.codigo as codigo_pessoa,
                               pessoa.nome as nome_pessoa
                          FROM pet
                          JOIN pessoa
                            ON pessoa.codigo = pet.codigo_pessoa
                         WHERE pet.codigo = :codigo';
        $stmtDadosPet = $conexao->prepare($sqlDadosPet);
        $stmtDadosPet->bindValue(':codigo', $idParametro);
        $stmtDadosPet->execute();

        $dados = array();
        while($linha = $stmtDadosPet->fetch(PDO::FETCH_OBJ)) {
            $vacina = explode('-',$linha->vacina);
            $vacina = $vacina[2].'/'.$vacina[1].'/'.$vacina[0];

            echo $vacina;

            $dados = array('codigo' => $linha->codigo,
                           'nome' => $linha->nome,
                           'raca' => $linha->raca,
                           'especie' => $linha->especie,
                           'caracteristica' => $linha->caracteristica,
                           'idade' => $linha->idade,
                           'tipo_idade' => $linha->tipo_idade,
                           'peso' => $linha->peso,
                           'sexo' => $linha->sexo,
                           'pelagem' => $linha->pelagem,
                           'vacina' => $vacina,
                           'codigo_pessoa' => $linha->codigo_pessoa,
                           'nome_pessoa' => $linha->nome_pessoa);
        }

        if(!$dados) {
            include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
        }
        else {
            require_once(Constante::DIRETORIO_ROOT.'/view/Pet/Alterar.php');
        }
    }
    else {
        require_once(Constante::DIRETORIO_ROOT.'/view/Pet/Alterar.php');
    }
}
else if($acaoPagina == Constante::ACAO_EXCLUIR) {
    $sqlExcluirPet = 'DELETE FROM pet WHERE codigo = :codigo';
    $stmtExcluirPet = $conexao->prepare($sqlExcluirPet);
    $stmtExcluirPet->bindValue(':codigo', $idParametro);
    $stmtExcluirPet->execute();

    $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_EXCLUIDO;
    executaJs('location.href="'.Constante::LINK.'/pet"');
    exit;
}
