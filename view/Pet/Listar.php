<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-paw"></i> Listar <small>Pets</small></h1>
            </div>
            <div class="row margem-inferior-10">
                <div class="col-xs-1">
                    <a href="<?=Constante::LINK.'/pet/incluir'?>" class="btn btn-primary"><span class="fa fa-plus" aria-hidden="true"></span> Incluir</a>
                </div>
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
                <div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover tabela-consulta">
                        <thead>
                            <tr>
                                <th class="hidden">código</th>
                                <th>Nome</th>
                                <th>Raca</th>
                                <th>Proprietário</th>
                                <th class="titulo-acao">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach($dados as $pet) {
                        ?>
                                <tr>
                                    <td class="hidden"><?=$pet['codigo']?></td>
                                    <td><?=$pet['nome']?></td>
                                    <td><?=$pet['raca']?></td>
                                    <td><?=$pet['nome_pessoa']?></td>
                                    <td class="acao">
                                        <a href="<?=Constante::LINK.'/pet/alterar/'.$pet['codigo']?>" class="btn-acao-alterar btn btn-info">Alterar</a>
                                    <?php
                                        if(isUsuarioAdministrador()) {
                                    ?>
                                            <a href="<?=Constante::LINK.'/pet/excluir/'.$pet['codigo']?>" class="btn-acao-excluir btn btn-danger">Excluir</a>
                                    <?php
                                        }
                                    ?>
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
