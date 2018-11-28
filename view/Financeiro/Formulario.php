<?php
if(isset($_POST['codigo'])) {
    $codigo = $_POST['codigo[]'];

    $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_ALTERADO;
    executaJs('location.href="'.Constante::LINK.'/financeiro"');
    exit;
}


if($acaoPagina == Constante::ACAO_ALTERAR) {
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-money"></i> <?=($acaoPagina=="alterar")?"Visualizar":"Incluir";?> <small>Parcelas (<?=$dados[0]['nome_pessoa']?>)</small></h1>
            </div>
            <form role="form" action="<?=Constante::LINK.'/financeiro/'.$acaoPagina?>" method="post">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="hidden">Código</th>
                                                <th>Número da parcela</th>
                                                <th>Data da parcela</th>
                                                <th>Valor da parcela</th>
                                                <th>Situação</th>
                                                <?php
                                                if(isUsuarioAdministrador()) {
                                                ?>
                                                    <th>Ações</th>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(count($dados) >= 1) {
                                                    foreach($dados as $financeiro) {
                                                        $parcelaMostrar = 'Quitada';
                                                        if($financeiro['situacao_parcela'] == 1) {
                                                            $parcelaMostrar = 'Em aberto';
                                                        }

                                            ?>
                                                        <tr>
                                                            <td class="hidden" rel="codigo"><?=$financeiro['codigo']?></td>
                                                            <td><?=$financeiro['numero_parcela']?></td>
                                                            <td><?=exibeData($financeiro['data_parcela'])?></td>
                                                            <td>R$ <?=$financeiro['valor_parcela']?></td>
                                                            <td><?=$parcelaMostrar?></td>
                                                            <?php
                                                            if(isUsuarioAdministrador()) {
                                                                if($financeiro['situacao_parcela'] == 1) {
                                                            ?>
                                                                    <td class="acao"><a href="" class="btn-acao-alterar btn btn-info btn-contrato acao-parcela" rel="baixa-parcela">Baixar</a></td>
                                                            <?php
                                                                }
                                                                else {
                                                            ?>
                                                                    <td class="acao"><a href="" class="btn-acao-alterar btn btn-warning acao-parcela" rel="estorno-parcela">Estornar</a></td>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr />
                        <a href="<?=Constante::LINK.'/financeiro'?>" class="btn btn-secundar btn-voltar">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
}
