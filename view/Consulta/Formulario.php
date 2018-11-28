<?php
if(isset($_POST['codigo-pessoa'])) {
    $codigo = filter_var(trim($_POST['codigo']), FILTER_SANITIZE_NUMBER_INT);
    $codigoPessoa = filter_var(trim($_POST['codigo-pessoa']), FILTER_SANITIZE_NUMBER_INT);
    $codigoPet = filter_var(trim($_POST['pet']), FILTER_SANITIZE_NUMBER_INT);
    $observacoes = filter_var(trim($_POST['observacoes']), FILTER_SANITIZE_STRING);

    $procedimentos = $_POST['procedimento'];

    /*
     * Insere
     */
    if(empty($codigo)) {
        $sqlInsereConsulta = 'INSERT INTO consulta (codigo_pessoa,
                                                    codigo_pet,
                                                    observacoes)
                                            VALUES (:codigo_pessoa,
                                                    :codigo_pet,
                                                    :observacoes)';

        $stmtInsereConsulta = $conexao->prepare($sqlInsereConsulta);
        $stmtInsereConsulta->bindValue(':codigo_pessoa', $codigoPessoa);
        $stmtInsereConsulta->bindValue(':codigo_pet', $codigoPet);
        $stmtInsereConsulta->bindValue(':observacoes', $observacoes);
        $stmtInsereConsulta->execute();

        $sqlUltimaConsulta = 'SELECT max(codigo) as ultimo FROM consulta';
        $stmtUltimaConsulta = $conexao->prepare($sqlUltimaConsulta);
        $stmtUltimaConsulta->execute();

        $ultimaConsulta = null;
        while($linha = $stmtUltimaConsulta->fetch(PDO::FETCH_OBJ)) {
            $ultimaConsulta = $linha->ultimo;
        }

        foreach($procedimentos as $procedimento) {
            $sqlInsereProcedimento = 'INSERT INTO procedimento_consulta (codigo_consulta,
                                                                         codigo_procedimento)
                                                                 VALUES (:codigo_consulta,
                                                                         :codigo_procedimento)';

            $stmtInsereProcedimento = $conexao->prepare($sqlInsereProcedimento);
            $stmtInsereProcedimento->bindValue(':codigo_consulta', $ultimaConsulta);
            $stmtInsereProcedimento->bindValue(':codigo_procedimento', $procedimento);
            $stmtInsereProcedimento->execute();
        }

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_INCLUIDO;
        executaJs('location.href="'.Constante::LINK.'/consulta"');
        exit;
    }
    else {
        $sqlAlteraConsulta = 'UPDATE consulta
                                 SET codigo_pessoa = :codigo_pessoa,
                                     codigo_pet = :codigo_pet,
                                     observacoes = :observacoes
                               WHERE codigo = :codigo';
        $stmtAlteraConsulta = $conexao->prepare($sqlAlteraConsulta);
        $stmtAlteraConsulta->bindValue(':codigo', $codigo);
        $stmtAlteraConsulta->bindValue(':codigo_pessoa', $codigoPessoa);
        $stmtAlteraConsulta->bindValue(':codigo_pet', $codigoPet);
        $stmtAlteraConsulta->bindValue(':observacoes', $observacoes);
        $stmtAlteraConsulta->execute();

        $sqlDeletaProcedimento = 'DELETE FROM procedimento_consulta WHERE codigo_consulta = :codigo_consulta';
        $stmtDeletaProcedimento = $conexao->prepare($sqlDeletaProcedimento);
        $stmtDeletaProcedimento->bindValue(':codigo_consulta', $codigo);
        $stmtDeletaProcedimento->execute();

        foreach($procedimentos as $procedimento) {
            $sqlInsereProcedimento = 'INSERT INTO procedimento_consulta (codigo_consulta,
                                                                         codigo_procedimento)
                                                                 VALUES (:codigo_consulta,
                                                                         :codigo_procedimento)';

            $stmtInsereProcedimento = $conexao->prepare($sqlInsereProcedimento);
            $stmtInsereProcedimento->bindValue(':codigo_consulta', $codigo);
            $stmtInsereProcedimento->bindValue(':codigo_procedimento', $procedimento);
            $stmtInsereProcedimento->execute();
        }

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_ALTERADO;
        executaJs('location.href="'.Constante::LINK.'/consulta"');
        exit;
    }
}

$codigo = null;
$codigoPessoa = null;
$nomePessoa = null;
$codigoPet = null;
$nomePet = null;
$observacoes = null;
$disabled = null;
$disabledVisualizar = null;
$dadosProcedimentos = [];
$procedimentos = [];

$sqlProcedimentos = 'SELECT codigo,
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
                       FROM procedimento
                      WHERE 1=1';
$stmtProcedimentos = $conexao->prepare($sqlProcedimentos);
$stmtProcedimentos->execute();
while($linha = $stmtProcedimentos->fetch(PDO::FETCH_OBJ)) {
    $procedimentos[] = array('codigo' => $linha->codigo, 'nome' => $linha->nome, 'peso' => $linha->peso);
}


if($acaoPagina == Constante::ACAO_ALTERAR || $acaoPagina == Constante::ACAO_VISUALIZAR) {
    if(isset($dados)) {
        $codigo = $dados['codigo'];
        $observacoes = $dados['observacoes'];
        $codigoPessoa = $dados['codigo_pessoa'];
        $nomePessoa = $dados['nome_pessoa'];
        $codigoPet = $dados['codigo_pet'];
        $nomePet = $dados['nome_pet'];
        $dadosProcedimentos = $dados['dadosProcedimentos'];
        $disabled = 'disabled';

        if($acaoPagina == Constante::ACAO_VISUALIZAR) {
            $disabledVisualizar = 'disabled';
        }
    }
    else {
        executaJs('location.href="'.Constante::LINK.'/consulta"');
        exit;
    }
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-stethoscope"></i> <?=ucfirst($acaoPagina)?> <small>Consultas</small></h1>
            </div>
            <form role="form" action="<?=Constante::LINK.'/consulta/'.$acaoPagina?>" method="post" id="consulta">
                <input type="hidden" value="<?=$codigo?>" name="codigo">
                <input type="hidden" value="<?=$codigoPessoa?>" name="codigo-pessoa">

                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Cliente</label>
                            <input type="text" <?=$disabled?> id="nome-pessoa" rel="consulta" autocomplete="off" class="form-control" value="<?=$nomePessoa?>" placeholder="Cliente">
                            <div class="resultado-busca-pessoa" id="consulta"></div>
                        </div>
                    </div>
                    <?php
                    if(!empty($codigoPet)) {
                    ?>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Pet</label>
                                <select class="form-control" <?=$disabledVisualizar?> name="pet"><option value="<?=$codigoPet?>"><?=$nomePet?></option></select>
                            </div>
                        </div>
                    <?php
                    }
                    else {
                    ?>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Pet</label>
                            <select class="form-control" <?=$disabledVisualizar?> name="pet"><option value="">Informe o cliente</option></select>
                        </div>
                    </div>
                    <?php
                  }
                    ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea class="form-control" <?=$disabledVisualizar?> name="observacoes" placeholder="Obervações"><?=$observacoes?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label>Procedimentos</label>
                        <div class="grid" id="grid-procedimento">
                            <?php
    if(count($dadosProcedimentos) >= 1) {
        foreach($dadosProcedimentos as $procedimento) {
                            ?>
                            <div class="container-grid">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <select class="form-control" <?=$disabledVisualizar?> name="procedimento[]">
                                                <?php
            foreach($procedimentos as $procedimentoCadastrado) {
                $selected = null;
                if($procedimentoCadastrado['codigo'] == $procedimento['codigo']) {
                    $selected = 'selected="selected"';
                }
                                                ?>
                                                <option <?=$selected?> value="<?=$procedimentoCadastrado['codigo']?>"><?=$procedimentoCadastrado['nome']. ' '.$procedimentoCadastrado['peso']?></option>
                                                <?php
            }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <a class="btn btn-danger btn-del">-</a>
                                        <a class="btn btn-primary btn-add">+</a>
                                    </div>
                                </div>
                            </div>
                            <?php
        }
    }
                                else {
                                    $htmlLista = null;
                                    foreach($procedimentos as $procedimento) {
                                        $htmlLista .= '<option value="'.$procedimento['codigo'].'">'.$procedimento['nome'].' '.$procedimento['peso'].'</option>';
                                    }
                            ?>
                            <div class="container-grid">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <select class="form-control" <?=$disabledVisualizar?> name="procedimento[]">
                                                <?=$htmlLista?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <a class="btn btn-danger btn-del">-</a>
                                        <a class="btn btn-primary btn-add">+</a>
                                    </div>
                                </div>
                                </d`iv>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <hr />
                    <?php
                        if(empty($disabledVisualizar)) {
                    ?>
                            <input type="submit" name="cadastrar" class="btn btn-primary" value="<?=ucfirst($acaoPagina)?>"/>
                    <?php
                        }
                    ?>
                    <a href="<?=Constante::LINK.'/consulta'?>" class="btn btn-secundar btn-voltar">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>
