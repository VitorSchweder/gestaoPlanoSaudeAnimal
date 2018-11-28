<?php
if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(isset($_POST['codigo-pessoa'])) {
    $codigo = filter_var(trim($_POST['codigo']), FILTER_SANITIZE_NUMBER_INT);
    $codigoPessoa = filter_var(trim($_POST['codigo-pessoa']), FILTER_SANITIZE_NUMBER_INT);
    $codigoPlano = filter_var(trim($_POST['plano']), FILTER_SANITIZE_NUMBER_INT);
    $diaPrimeiraParcela = filter_var(trim($_POST['dia-primeira-parcela']), FILTER_SANITIZE_NUMBER_INT);
    $diaPrimeiraParcela = str_pad($diaPrimeiraParcela, 2, '0', STR_PAD_LEFT);

    $pets = $_POST['pet'];

    $sqlLimitePetsPlano = 'SELECT limite_pet FROM plano WHERE codigo = :codigo';
    $stmtLimitePetsPlano = $conexao->prepare($sqlLimitePetsPlano);
    $stmtLimitePetsPlano->bindValue(':codigo', $codigoPlano);
    $stmtLimitePetsPlano->execute();

    $totalPetPlano = 0;
    while($linha = $stmtLimitePetsPlano->fetch(PDO::FETCH_OBJ)) {
        $totalPetPlano = $linha->limite_pet;
    }

    if(count($pets) > $totalPetPlano) {
        executaJs('alert("Total de pets ultrapassa limite plano, será cobrado adicional de 20% para cada pet incluso.")');
        if(empty($codigo)) {
            executaJs('location.href="'.Constante::LINK.'/contrato/incluir/"');
        }
        else {
            executaJs('location.href="'.Constante::LINK.'/contrato/alterar/'.$codigo.'"');
        }
    }
    else {
        /*
         * Insere
         */
        if(empty($codigo)) {
            $validade = new DateTime();
            $validade->add(new DateInterval('P1Y'));

            $sqlUltimoContrato = 'SELECT max(codigo) as ultimo FROM contrato';
            $stmtUltimoContrato = $conexao->prepare($sqlUltimoContrato);
            $stmtUltimoContrato->execute();

            $ultimoContrato = null;
            while($linha = $stmtUltimoContrato->fetch(PDO::FETCH_OBJ)) {
                $ultimoContrato = $linha->ultimo + 1;
            }

            if($ultimoContrato == 1) {
                $ultimoContrato = 2017001001;
            }

            calcularParcelas(12, $diaPrimeiraParcela.'/'.date('m').'/'.date('Y'), $ultimoContrato, $codigoPlano, $conexao, 'incluir', count($pets));

            $sqlInsereContrato = 'INSERT INTO contrato (codigo,
                                                        codigo_pessoa,
                                                        codigo_plano,
                                                        validade,
                                                        dia_primeira_parcela)
                                                VALUES (:codigo,
                                                        :codigo_pessoa,
                                                        :codigo_plano,
                                                        :validade,
                                                        :dia_primeira_parcela)';

            $stmtInsereContrato = $conexao->prepare($sqlInsereContrato);
            $stmtInsereContrato->bindValue(':codigo', $ultimoContrato);
            $stmtInsereContrato->bindValue(':codigo_pessoa', $codigoPessoa);
            $stmtInsereContrato->bindValue(':codigo_plano', $codigoPlano);
            $stmtInsereContrato->bindValue(':dia_primeira_parcela', $diaPrimeiraParcela);
            $stmtInsereContrato->bindValue(':validade', $validade->format('Y-m-d'));
            $stmtInsereContrato->execute();

            $sqlUltimoContrato = 'SELECT max(codigo) as ultimo FROM contrato';
            $stmtUltimoContrato = $conexao->prepare($sqlUltimoContrato);
            $stmtUltimoContrato->execute();

            $ultimoContrato = null;
            while($linha = $stmtUltimoContrato->fetch(PDO::FETCH_OBJ)) {
                $ultimoContrato = $linha->ultimo;
            }


            foreach($pets as $pet) {
                $sqlInserePetContrato = 'INSERT INTO pet_contrato (codigo_contrato,
                                                                   codigo_pet)
                                                           VALUES (:codigo_contrato,
                                                                   :codigo_pet)';

                $stmtInserePetContrato = $conexao->prepare($sqlInserePetContrato);
                $stmtInserePetContrato->bindValue(':codigo_contrato', $ultimoContrato);
                $stmtInserePetContrato->bindValue(':codigo_pet', $pet);
                $stmtInserePetContrato->execute();
            }

            $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_INCLUIDO;
            executaJs('location.href="'.Constante::LINK.'/contrato"');
            exit;
        }
        else {
            $sqlAlteraContrato = 'UPDATE contrato
                                     SET codigo_plano = :codigo_plano,
                                         codigo_pessoa = :codigo_pessoa,
                                         dia_primeira_parcela = :dia_primeira_parcela
                                   WHERE codigo = :codigo';
            $stmtAlteraContrato = $conexao->prepare($sqlAlteraContrato);
            $stmtAlteraContrato->bindValue(':codigo', $codigo);
            $stmtAlteraContrato->bindValue(':codigo_pessoa', $codigoPessoa);
            $stmtAlteraContrato->bindValue(':codigo_plano', $codigoPlano);
            $stmtAlteraContrato->bindValue(':dia_primeira_parcela', $diaPrimeiraParcela);
            $stmtAlteraContrato->execute();

            calcularParcelas(12, null, $codigo, $codigoPlano, $conexao, 'alterar');

            $sqlDeletaPetContrato = 'DELETE FROM pet_contrato WHERE codigo_contrato = :codigo_contrato';
            $stmtDeletaPetContrato = $conexao->prepare($sqlDeletaPetContrato);
            $stmtDeletaPetContrato->bindValue(':codigo_contrato', $codigo);
            $stmtDeletaPetContrato->execute();

            foreach($pets as $pet) {
                $sqlInserePetContrato = 'INSERT INTO pet_contrato (codigo_contrato,
                                                                   codigo_pet)
                                                           VALUES (:codigo_contrato,
                                                                   :codigo_pet)';

                $stmtInsereContrato = $conexao->prepare($sqlInserePetContrato);
                $stmtInsereContrato->bindValue(':codigo_contrato', $codigo);
                $stmtInsereContrato->bindValue(':codigo_pet', $pet);
                $stmtInsereContrato->execute();
            }

            $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_ALTERADO;
            executaJs('location.href="'.Constante::LINK.'/contrato"');
            exit;
        }
    }
}

$codigo = null;
$codigoPessoa = null;
$nomePessoa = null;
$codigoPet = null;
$codigoPlano = null;
$diaPrimeiraParcela = null;
$disabled = null;
$dadosPets = [];
$pets = [];
$planos = [];

$sqlPlano = 'SELECT codigo,
                    nome
               FROM plano
              WHERE 1=1';
$stmtPlano = $conexao->prepare($sqlPlano);
$stmtPlano->execute();
while($linha = $stmtPlano->fetch(PDO::FETCH_OBJ)) {
    $planos[] = array('codigo' => $linha->codigo, 'nome' => $linha->nome);
}

if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(isset($dados)) {
        $disabled = 'disabled';
        $codigo = $dados['codigo'];
        $codigoPessoa = $dados['codigo_pessoa'];
        $nomePessoa = $dados['nome_pessoa'];
        $dadosPets = $dados['dadosPet'];
        $codigoPlano = $dados['codigo_plano'];
        $diaPrimeiraParcela = $dados['dia_primeira_parcela'];

        $sqlPets = 'SELECT codigo,
                           nome
                      FROM pet
                     WHERE codigo_pessoa = :codigo_pessoa';
        $stmtPet = $conexao->prepare($sqlPets);
        $stmtPet->bindValue(':codigo_pessoa', $codigoPessoa);
        $stmtPet->execute();
        while($linha = $stmtPet->fetch(PDO::FETCH_OBJ)) {
            $pets[] = array('codigo' => $linha->codigo, 'nome' => $linha->nome);
        }
    }
    else {
        executaJs('location.href="'.Constante::LINK.'/contrato"');
        exit;
    }
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-file-text"></i> <?=($acaoPagina=="alterar")?"Alterar":"Incluir";?> <small>Contratos</small></h1>
            </div>
            <form role="form" action="<?=Constante::LINK.'/contrato/'.$acaoPagina?>" method="post">
                <input type="hidden" value="<?=$codigo?>" name="codigo">
                <input type="hidden" value="<?=$codigoPessoa?>" name="codigo-pessoa">

                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Cliente</label>
                            <input type="text" id="nome-pessoa" <?=$disabled?> rel="contrato" autocomplete="off" class="form-control" value="<?=$nomePessoa?>" placeholder="Cliente">
                            <div class="resultado-busca-pessoa" id="contrato"></div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="plano">Plano</label>
                            <select class="form-control" name="plano">
                            <?php
                                foreach($planos as $plano) {
                                    $selectedPlano = null;
                                    if($plano['codigo'] == $codigoPlano) {
                                        $selectedPlano = 'selected="selected"';
                                    }

                                    echo '<option '.$selectedPlano.' value="'.$plano['codigo'].'">'.$plano['nome'].'</option>';
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <label>Pets</label>
                        <div class="grid" id="grid-pet">
                        <?php
                            if(count($dadosPets) >= 1) {
                                foreach($dadosPets as $pet) {
                        ?>
                                    <div class="container-grid">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <select class="form-control" name="pet[]">
                                                    <?php
                                                        foreach($pets as $petCadastrado) {
                                                            $selected = null;
                                                            if($petCadastrado['codigo'] == $pet['codigo']) {
                                                                $selected = 'selected="selected"';
                                                            }
                                                    ?>
                                                            <option <?=$selected?> value="<?=$petCadastrado['codigo']?>"><?=$petCadastrado['nome']?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <a class="btn btn-danger btn-del">-</a>
                                                <a class="btn btn-primary btn-add">+</a>
                                            </div>
                                        </div>
                                    </div>
                        <?php
                                }
                            }
                        ?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Primeira Parcela (dia)</label>
                            <input type="text" <?=$disabled?> name="dia-primeira-parcela" autocomplete="off" class="form-control" value="<?=$diaPrimeiraParcela?>" placeholder="Primeira Parcela" maxlength="2">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <hr />
                        <input type="submit" name="cadastrar" class="btn btn-primary" value="<?=ucfirst($acaoPagina)?>"/>
                        <a href="<?=Constante::LINK.'/contrato'?>" class="btn btn-secundar btn-voltar">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
