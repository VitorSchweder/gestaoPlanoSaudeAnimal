<?php
if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(isset($_POST['nome'])) {
    $nome = filter_var(trim($_POST['nome']), FILTER_SANITIZE_STRING);
    $codigo = filter_var(trim($_POST['codigo']), FILTER_SANITIZE_NUMBER_INT);
    $porcentagemProcedimento = filter_var(trim($_POST['porcentagem-procedimento']), FILTER_SANITIZE_STRING);
    $porcentagemProcedimento = str_replace(',', '.', $porcentagemProcedimento);
    $valor = filter_var(trim($_POST['valor']), FILTER_SANITIZE_STRING);
    $valor = str_replace(',', '.', $valor);
    $limitePet = filter_var(trim($_POST['limite-pet']), FILTER_SANITIZE_NUMBER_INT);

    /*
     * Insere
     */
    if(empty($codigo)) {
        $sqlInserePlano = 'INSERT INTO plano (nome, valor, porcentagem_procedimento, limite_pet) VALUES (:nome, :valor, :porcentagem_procedimento, :limite_pet)';
        $stmtInserePlano = $conexao->prepare($sqlInserePlano);
        $stmtInserePlano->bindValue(':nome', $nome);
        $stmtInserePlano->bindValue(':porcentagem_procedimento', $porcentagemProcedimento);
        $stmtInserePlano->bindValue(':valor', $valor);
        $stmtInserePlano->bindValue(':limite_pet', $limitePet);
        $stmtInserePlano->execute();

        $sqlUltimoPlano = 'SELECT max(codigo) as ultimo FROM plano';
        $stmtUltimoPlano = $conexao->prepare($sqlUltimoPlano);
        $stmtUltimoPlano->execute();

        $ultimoPlano = 0;
        while($linha = $stmtUltimoPlano->fetch(PDO::FETCH_OBJ)) {
            $ultimoPlano = $linha->ultimo;
        }

        $procedimentos = $_POST['procedimento'];
        if(count($procedimentos) > 0) {
            foreach($procedimentos as $chave => $procedimento) {
                $sqlInsereProcedimento = 'INSERT INTO procedimento_plano (codigo_plano, codigo_procedimento, valor_procedimento)
                                                                  VALUES (:codigo_plano, :codigo_procedimento, :valor_procedimento)';
                $stmtInsereProcedimento = $conexao->prepare($sqlInsereProcedimento);
                $stmtInsereProcedimento->bindValue(':codigo_plano', $ultimoPlano);
                $stmtInsereProcedimento->bindValue(':codigo_procedimento', $procedimento);
                $stmtInsereProcedimento->bindValue(':valor_procedimento', str_replace(',', '.', $_POST['valor-fixo-desconto'][$chave]));
                $stmtInsereProcedimento->execute();
            }
        }

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_INCLUIDO;
        executaJs('location.href="'.Constante::LINK.'/plano"');
        exit;
    }
    else {
        $sqlDeletaProcedimentoPlano = 'DELETE FROM procedimento_plano WHERE codigo_plano = :codigo_plano';
        $stmtDeletaProcedimentoPlano = $conexao->prepare($sqlDeletaProcedimentoPlano);
        $stmtDeletaProcedimentoPlano->bindValue(':codigo_plano', $codigo);
        $stmtDeletaProcedimentoPlano->execute();

        $procedimentos = $_POST['procedimento'];
        if(count($procedimentos) > 0) {
            foreach($procedimentos as $chave => $procedimento) {
                $sqlInsereProcedimento = 'INSERT INTO procedimento_plano (codigo_plano, codigo_procedimento, valor_procedimento)
                                                                  VALUES (:codigo_plano, :codigo_procedimento, :valor_procedimento)';
                $stmtInsereProcedimento = $conexao->prepare($sqlInsereProcedimento);
                $stmtInsereProcedimento->bindValue(':codigo_plano', $codigo);
                $stmtInsereProcedimento->bindValue(':codigo_procedimento', $procedimento);
                $stmtInsereProcedimento->bindValue(':valor_procedimento', str_replace(',', '.', $_POST['valor-fixo-desconto'][$chave]));
                $stmtInsereProcedimento->execute();
            }
        }

        $sqlAlteraProcedimento = 'UPDATE plano
                                     SET nome = :nome,
                                         valor = :valor,
                                         porcentagem_procedimento = :porcentagem_procedimento,
                                         limite_pet = :limite_pet
                                   WHERE codigo = :codigo';
        $sqlAlteraProcedimento = $conexao->prepare($sqlAlteraProcedimento);
        $sqlAlteraProcedimento->bindValue(':nome', $nome);
        $sqlAlteraProcedimento->bindValue(':porcentagem_procedimento', $porcentagemProcedimento);
        $sqlAlteraProcedimento->bindValue(':valor', $valor);
        $sqlAlteraProcedimento->bindValue(':limite_pet', $limitePet);
        $sqlAlteraProcedimento->bindValue(':codigo', $codigo);
        $sqlAlteraProcedimento->execute();

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_ALTERADO;
        executaJs('location.href="'.Constante::LINK.'/plano"');
        exit;
    }
}

$codigo = null;
$nome = null;
$valor = null;
$limitePet = null;

$porcentagemProcedimento = null;
$dadosProcedimentos = [];

if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(isset($dados)) {
        $codigo = $dados['codigo'];
        $nome = $dados['nome'];
        $valor = $dados['valor'];
        $limitePet = $dados['limite_pet'];
        $valor = gravaMoeda($valor);
        $porcentagemProcedimento = $dados['porcentagem_procedimento'];
        $dadosProcedimentos = $dados['dadosProcedimentos'];
    }
    else {
        executaJs('location.href="'.Constante::LINK.'/plano"');
        exit;
    }
}
else {
    $dadosProcedimentos = $dados;
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-clone"></i> <?=($acaoPagina=="alterar")?"Alterar":"Incluir";?> <small>Planos</small></h1>
            </div>
            <form role="form" action="<?=Constante::LINK.'/plano/'.$acaoPagina?>" method="post">
                <input type="hidden" value="<?=$codigo?>" name="codigo">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?=$nome?>" placeholder="Nome">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Valor</label>
                            <input type="text" name="valor" class="form-control" value="<?=$valor?>" placeholder="Valor">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Desconto (%)</label>
                            <input type="text" name="porcentagem-procedimento" class="form-control" value="<?=$porcentagemProcedimento?>" placeholder="Desconto">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Limite de Pets</label>
                            <input type="text" name="limite-pet" class="form-control" value="<?=$limitePet?>" placeholder="Limite Pet">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <hr />
                        <label>Procedimentos</label>
                        <div class="grid" id="grid-procedimento">
                            <?php
    if(count($dadosProcedimentos) >= 1) {
        foreach($dadosProcedimentos as $procedimento) {
                            ?>
                            <div class="container-grid">
                                <input type="hidden" rel="tipo-desconto" value="<?=$procedimento['tipo']?>"/>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select class="form-control" name="procedimento[]">
                                                <option value="<?=$procedimento['codigo']?>"><?=$procedimento['nome'].' '.$procedimento['peso']?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" name="valor_procedimento[]" rel="valor" class="form-control" value="<?=$procedimento['valor_original']?>" placeholder="Valor procedimento" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" name="valor-fixo-desconto[]" rel="valor-fixo-desconto" class="form-control" value="<?=$procedimento['valor']?>" placeholder="Valor Fixo">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <a class="btn btn-danger btn-del">-</a>
                                    </div>
                                </div>
                            </div>
                            <?php
        }
    }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr />
                        <input type="submit" name="cadastrar" class="btn btn-primary" value="<?=ucfirst($acaoPagina)?>"/>
                        <a href="<?=Constante::LINK.'/plano'?>" class="btn btn-secundar btn-voltar">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
