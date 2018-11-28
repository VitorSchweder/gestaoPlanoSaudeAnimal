<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-money"></i> Listar <small>Financeiro</small></h1>
            </div>
            <div class="row margem-inferior-10">
                <?php
                    if(isset($_SESSION['MENSAGEM'])) {
                ?>
                <div class="col-xs-4">
                    <div class="alert alert-success">
                        <?=$_SESSION['MENSAGEM']?>
                    </div>
                </div>
                <?php
                        unset($_SESSION['MENSAGEM']);
                    }
                ?>
                </div>
                <div class="panel panel-default">
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover tabela-consulta">
                            <thead>
                                <tr>
                                    <th class="hidden">código</th>
                                    <th>Nome do cliente</th>
                                    <th>Número do contrato</th>
                                    <th>Situação</th>
                                    <th class="titulo-acao">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($dados as $financeiro) {
                            ?>
                                <tr>
                                    <td class="hidden"><?=$financeiro['nome_cliente']?></td>
                                    <td><?=$financeiro['nome_pessoa']?></td>
                                    <td><?=$financeiro['codigo_contrato']?></td>
                                    <td><?=$financeiro['situacao']?></td>
                                    <td class="acao">
                                        <a href="<?=Constante::LINK.'/financeiro/alterar/'.$financeiro['codigo_contrato']?>" class="btn-acao-alterar btn btn-info">Ver parcelas</a>
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
