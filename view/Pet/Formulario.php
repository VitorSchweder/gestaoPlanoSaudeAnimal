<?php
if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(isset($_POST['nome'])) {
    $nome = filter_var(trim($_POST['nome']), FILTER_SANITIZE_STRING);
    $codigo = filter_var(trim($_POST['codigo']), FILTER_SANITIZE_NUMBER_INT);
    $raca = filter_var(trim($_POST['raca']), FILTER_SANITIZE_STRING);
    $especie = filter_var(trim($_POST['especie']), FILTER_SANITIZE_STRING);
    $caracteristica = filter_var(trim($_POST['caracteristica']), FILTER_SANITIZE_STRING);
    $idade = filter_var(trim($_POST['idade']), FILTER_SANITIZE_STRING);
    $tipoIdade = filter_var(trim($_POST['tipo-idade']), FILTER_SANITIZE_STRING);
    $peso = filter_var(trim($_POST['peso']), FILTER_SANITIZE_STRING);
    $sexo = filter_var(trim($_POST['sexo']), FILTER_SANITIZE_STRING);
    $pelagem = filter_var(trim($_POST['pelagem']), FILTER_SANITIZE_STRING);
    $codigoPessoa = filter_var(trim($_POST['codigo-pessoa']), FILTER_SANITIZE_NUMBER_INT);
    $vacina = filter_var(trim($_POST['vacina']), FILTER_SANITIZE_STRING);

    $vacina = explode('/', $vacina);
    $vacina = $vacina[2].'-'.$vacina[1].'-'.$vacina[0];
    /*
     * Insere
     */
    if(empty($codigo)) {
        $sqlInserePet = 'INSERT INTO pet (nome,
                                          raca,
                                          caracteristica,
                                          idade,
                                          tipo_idade,
                                          peso,
                                          sexo,
                                          pelagem,
                                          especie,
                                          codigo_pessoa,
                                          vacina)
                                  VALUES (:nome,
                                          :raca,
                                          :caracteristica,
                                          :idade,
                                          :tipo_idade,
                                          :peso,
                                          :sexo,
                                          :pelagem,
                                          :especie,
                                          :codigo_pessoa,
                                          :vacina)';
        $stmtInserePet = $conexao->prepare($sqlInserePet);
        $stmtInserePet->bindValue(':nome', $nome);
        $stmtInserePet->bindValue(':raca', $raca);
        $stmtInserePet->bindValue(':caracteristica', $caracteristica);
        $stmtInserePet->bindValue(':idade', $idade);
        $stmtInserePet->bindValue(':tipo_idade', $tipoIdade);
        $stmtInserePet->bindValue(':peso', $peso);
        $stmtInserePet->bindValue(':sexo', $sexo);
        $stmtInserePet->bindValue(':pelagem', $pelagem);
        $stmtInserePet->bindValue(':especie', $especie);
        $stmtInserePet->bindValue(':vacina', $vacina);
        $stmtInserePet->bindValue(':codigo_pessoa', $codigoPessoa);
        $stmtInserePet->execute();

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_INCLUIDO;
        executaJs('location.href="'.Constante::LINK.'/pet"');
        exit;
    }
    else {
        $sqlAlteraPet = 'UPDATE pet
                            SET nome = :nome,
                                raca = :raca,
                                caracteristica = :caracteristica,
                                idade = :idade,
                                tipo_idade = :tipo_idade,
                                peso = :peso,
                                sexo = :sexo,
                                pelagem = :pelagem,
                                especie = :especie,
                                codigo_pessoa = :codigo_pessoa,
                                vacina = :vacina
                          WHERE codigo = :codigo';
        $stmtAlteraPet = $conexao->prepare($sqlAlteraPet);
        $stmtAlteraPet->bindValue(':codigo', $codigo);
        $stmtAlteraPet->bindValue(':nome', $nome);
        $stmtAlteraPet->bindValue(':raca', $raca);
        $stmtAlteraPet->bindValue(':caracteristica', $caracteristica);
        $stmtAlteraPet->bindValue(':idade', $idade);
        $stmtAlteraPet->bindValue(':tipo_idade', $tipoIdade);
        $stmtAlteraPet->bindValue(':peso', $peso);
        $stmtAlteraPet->bindValue(':sexo', $sexo);
        $stmtAlteraPet->bindValue(':pelagem', $pelagem);
        $stmtAlteraPet->bindValue(':especie', $especie);
        $stmtAlteraPet->bindValue(':vacina', $vacina);
        $stmtAlteraPet->bindValue(':codigo_pessoa', $codigoPessoa);
        $stmtAlteraPet->execute();

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_ALTERADO;
        executaJs('location.href="'.Constante::LINK.'/pet"');
        exit;
    }
}

$codigo = null;
$nome = null;
$raca = null;
$caracteristica = null;
$idade = null;
$tipoIdade = null;
$peso = null;
$sexo = null;
$pelagem = null;
$codigoPessoa = null;
$nomePessoa = null;
$vacina = null;
$selectedMacho = null;
$selectedFemea = null;
$selected5 = null;
$selected5_10 = null;
$selected10_20 = null;
$selected20 = null;
$selectedPequeno = null;
$selectedMedio = null;
$selectedGrande = null;
$selectedAnos = null;
$selectedMeses = null;
$selectedCanino = null;
$selectedFelino = null;

if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(isset($dados)) {
        $codigo = $dados['codigo'];
        $nome = $dados['nome'];
        $raca = $dados['raca'];
        $caracteristica = $dados['caracteristica'];
        $idade = $dados['idade'];
        $tipoIdade = $dados['tipo_idade'];
        $peso = $dados['peso'];
        $pelagem = $dados['pelagem'];
        $codigoPessoa = $dados['codigo_pessoa'];
        $nomePessoa = $dados['nome_pessoa'];
        $vacina = $dados['vacina'];

        if($dados['sexo'] == 1) {
            $selectedMacho = 'selected="selected"';
        }
        else if($dados['sexo'] == 2) {
            $selectedFemea = 'selected="selected"';
        }

        if($dados['peso'] == 1) {
            $selected5 = 'selected="selected"';
        }
        else if($dados['peso'] == 2) {
            $selected5_10 = 'selected="selected"';
        }
        else if($dados['peso'] == 3) {
            $selected10_20 = 'selected="selected"';
        }
        else if($dados['peso'] == 4) {
            $selected20 = 'selected="selected"';
        }

        if($dados['tipo_idade'] == 1) {
            $selectedAnos = 'selected="selected"';
        }
        else if($dados['tipo_idade'] == 2) {
            $selectedMeses = 'selected="selected"';
        }

        if($dados['especie'] == 1) {
            $selectedCanino = 'selected="selected"';
        }
        else if($dados['especie'] == 2) {
            $selectedFelino = 'selected="selected"';
        }
    }
    else {
        executaJs('location.href="'.Constante::LINK.'/pet"');
        exit;
    }
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-paw"></i> <?=($acaoPagina=="alterar")?"Alterar":"Incluir";?> <small>Pets</small></h1>
            </div>
            <form role="form" action="<?=Constante::LINK.'/pet/'.$acaoPagina?>" method="post" id="pet">
                <input type="hidden" value="<?=$codigo?>" name="codigo">
                <input type="hidden" value="<?=$codigoPessoa?>" name="codigo-pessoa">

                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Proprietário</label>
                            <input type="text" id="nome-pessoa" autocomplete="off" class="form-control" value="<?=$nomePessoa?>" placeholder="Proprietário">
                            <div class="resultado-busca-pessoa"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Nome do Pet</label>
                            <input type="text" name="nome" class="form-control" value="<?=$nome?>" placeholder="Nome">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Raça</label>
                            <input type="text" name="raca" class="form-control" value="<?=$raca?>" placeholder="Raça">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label>Idade</label>
                            <input type="text" name="idade" class="form-control" value="<?=$idade?>" placeholder="Idade">
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label for="tipo-idade">Tipo</label>
                            <select class="form-control" name="tipo-idade">
                                <option value="1" <?=$selectedAnos?>>Anos</option>
                                <option value="2" <?=$selectedMeses?>>Meses</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="peso">Peso</label>
                            <select class="form-control" name="peso">
                                <option value="1" <?=$selected5?>>Até 5kg</option>
                                <option value="2" <?=$selected5_10?>>De 5 a 10kg</option>
                                <option value="3" <?=$selected10_20?>>De 10 a 20kg</option>
                                <option value="4" <?=$selected20?>>Acima de 20kg</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Pelagem</label>
                            <input type="text" name="pelagem" class="form-control" value="<?=$pelagem?>" placeholder="Pelagem">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Última Vacina</label>
                            <input type="text" name="vacina" class="form-control" value="<?=$vacina?>" placeholder="Última Vacina">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select class="form-control" name="sexo">
                                <option value="1" <?=$selectedMacho?>>Macho</option>
                                <option value="2" <?=$selectedFemea?>>Fêmea</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="especie">Espécie</label>
                            <select class="form-control" name="especie">
                                <option value="1" <?=$selectedCanino?>>Canino</option>
                                <option value="2" <?=$selectedFelino?>>Felino</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>Caracteristicas</label>
                            <textarea class="form-control" name="caracteristica" placeholder="Caracteristica" noresize><?=$caracteristica?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr />
                        <input type="submit" name="cadastrar" class="btn btn-primary" value="<?=ucfirst($acaoPagina)?>"/>
                        <a href="<?=Constante::LINK.'/pet'?>" class="btn btn-secundar btn-voltar">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
