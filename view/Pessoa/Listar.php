<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-user"></i> Listar <small>Pessoas</small></h1>
            </div>
            <div class="row margem-inferior-10">
                <div class="col-xs-1">
                    <a href="<?=Constante::LINK.'/pessoa/incluir'?>" class="btn btn-primary"><span class="fa fa-plus" aria-hidden="true"></span> Incluir</a>
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
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover tabela-consulta">
                        <thead>
                            <tr>
                                <th class="hidden">código</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Tipo</th>
                                <th>Endereco</th>
                                <th class="titulo-acao">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($dados as $pessoa) {
                            ?>
                            <tr>
                                <td class="hidden"><?=$pessoa['codigo']?></td>
                                <td><?=$pessoa['nome']?></td>
                                <td><?=$pessoa['cpf']?></td>
                                <td><?=$pessoa['tipo']?></td>
                                <td><?=$pessoa['endereco']?></td>
                                <td class="acao">
                                    <a href="<?=Constante::LINK.'/pessoa/alterar/'.$pessoa['codigo']?>" class="btn-acao-alterar btn btn-info">Alterar</a>
                                    <?php
                                if(isUsuarioAdministrador()) {
                                    ?>
                                    <a href="<?=Constante::LINK.'/pessoa/excluir/'.$pessoa['codigo']?>" class="btn-acao-excluir-pessoa btn btn-danger">Excluir</a>
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
