<?php
if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(isset($_POST['nome'])) {
    $nome = filter_var(trim($_POST['nome']), FILTER_SANITIZE_STRING);
    $codigo = filter_var(trim($_POST['codigo']), FILTER_SANITIZE_NUMBER_INT);
    $peso = filter_var(trim($_POST['peso']), FILTER_SANITIZE_NUMBER_INT);
    $tipo = filter_var(trim($_POST['tipo']), FILTER_SANITIZE_NUMBER_INT);
    $valor = filter_var(trim($_POST['valor']), FILTER_SANITIZE_STRING);
    $valor = gravaMoeda($valor);

    /*
     * Insere
     */
    if(empty($codigo)) {
        $sqlInsereProcedimento = 'INSERT INTO procedimento (nome, peso, valor, tipo) VALUES (:nome, :peso, :valor, :tipo)';
        $stmtInsereProcedimento = $conexao->prepare($sqlInsereProcedimento);
        $stmtInsereProcedimento->bindValue(':nome', $nome);
        $stmtInsereProcedimento->bindValue(':peso', $peso);
        $stmtInsereProcedimento->bindValue(':valor', $valor);
        $stmtInsereProcedimento->bindValue(':tipo', $tipo);
        $stmtInsereProcedimento->execute();

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_INCLUIDO;
        executaJs('location.href="'.Constante::LINK.'/procedimento"');
        exit;
    }
    else {
        $sqlAlteraProcedimento = 'UPDATE procedimento
                                     SET nome = :nome,
                                         peso = :peso,
                                         valor = :valor,
                                         tipo = :tipo
                                   WHERE codigo = :codigo';
        $sqlAlteraProcedimento = $conexao->prepare($sqlAlteraProcedimento);
        $sqlAlteraProcedimento->bindValue(':nome', $nome);
        $sqlAlteraProcedimento->bindValue(':peso', $peso);
        $sqlAlteraProcedimento->bindValue(':valor', $valor);
        $sqlAlteraProcedimento->bindValue(':tipo', $tipo);
        $sqlAlteraProcedimento->bindValue(':codigo', $codigo);
        $sqlAlteraProcedimento->execute();

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_ALTERADO;
        executaJs('location.href="'.Constante::LINK.'/procedimento"');
        exit;
    }
}

$codigo = null;
$nome = null;
$valor = null;
$selected0 = null;
$selected5 = null;
$selected5_10 = null;
$selected10_20 = null;
$selected20 = null;

$selectedTipoFixo = null;
$selectedTipoPorcentagem = null;

if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(isset($dados)) {
        $codigo = $dados['codigo'];
        $nome = $dados['nome'];
        $valor = $dados['valor'];

        if($dados['peso'] == 0) {
            $selected0 = 'selected="selected"';
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

        if($dados['tipo'] == 1) {
            $selectedTipoFixo = 'selected="selected"';
        }
        else if($dados['tipo'] == 2) {
            $selectedTipoPorcentagem = 'selected="selected"';
        }
    }
    else {
        executaJs('location.href="'.Constante::LINK.'/procedimento"');
        exit;
    }
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-bars"></i> <?=($acaoPagina=="alterar")?"Alterar":"Incluir";?> <small>Procedimentos</small></h1>
            </div>
            <form role="form" action="<?=Constante::LINK.'/procedimento/'.$acaoPagina?>" method="post">
                <input type="hidden" value="<?=$codigo?>" name="codigo">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?=$nome?>" placeholder="Nome">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Valor</label>
                            <input type="text" name="valor" class="form-control" value="<?=$valor?>" placeholder="Valor">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="peso">Peso</label>
                            <select class="form-control" name="peso">
                                <option value="0" <?=$selected0?>>Indiferente</option>
                                <option value="1" <?=$selected5?>>Até 5kg</option>
                                <option value="2" <?=$selected5_10?>>De 5 a 10kg</option>
                                <option value="3" <?=$selected10_20?>>De 10 a 20kg</option>
                                <option value="4" <?=$selected20?>>Acima de 20kg</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="tipo">Tipo desconto</label>
                            <select class="form-control" name="tipo">
                                <option value="1" <?=$selectedTipoFixo?>>Fixo</option>
                                <option value="2" <?=$selectedTipoPorcentagem?>>Porcentagem</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr />
                        <input type="submit" name="cadastrar" class="btn btn-primary" value="<?=ucfirst($acaoPagina)?>"/>
                        <a href="<?=Constante::LINK.'/procedimento'?>" class="btn btn-secundar btn-voltar">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
