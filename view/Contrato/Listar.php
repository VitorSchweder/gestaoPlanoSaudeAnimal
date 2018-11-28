<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-file-text"></i> Listar <small>Contratos</small></h1>
            </div>
            <div class="row margem-inferior-10">
                <div class="col-xs-1">
                <?php
                    if(!isUsuarioCliente()) {
                ?>
                        <a href="<?=Constante::LINK.'/contrato/incluir'?>" class="btn btn-primary"><span class="fa fa-plus" aria-hidden="true"></span> Incluir</a>
                <?php
                    }
                ?>                    
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
                                    <th>Validade</th>
                                    <th>Nome do cliente</th>
                                    <th>Número do contrato</th>
                                    <th class="titulo-acao">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($dados as $contrato) {
                                    $disabled = null;

                                    $linkEncerrar = Constante::LINK.'/contrato/excluir/'.$contrato['codigo'];
                                    $linkAlterar = Constante::LINK.'/contrato/alterar/'.$contrato['codigo'];
                                    if(!empty($contrato['data_encerramento'])) {
                                        $disabled = 'disabled';
                                        $linkEncerrar = null;
                                        $linkAlterar = null;
                                    }
                                ?>
                                <tr>
                                    <td class="hidden"><?=$contrato['codigo']?></td>
                                    <td><?=$contrato['data']?></td>
                                    <td><?=$contrato['nome_pessoa']?></td>
                                    <td><?=$contrato['codigo']?></td>
                                    <td class="acao">
                                        <a href="<?=Constante::LINK.'/exibeContrato?codigo='.$contrato['codigo']?>" class="btn-acao-alterar btn btn-info btn-contrato">Visualizar</a>
                                    <?php
                                        if(!isUsuarioCliente()) {
                                    ?>
                                            <a href="<?=Constante::LINK.'/exibeEtiqueta?codigo='.$contrato['codigo']?>" class="btn-acao-alterar btn btn-warning">Etiqueta</a>
                                            <a href="<?=$linkAlterar?>" <?=$disabled?> class="btn-acao-alterar btn btn-info">Alterar</a>
                                    <?php
                                        }
                                        if(isUsuarioAdministrador()) {
                                    ?>
                                            <a href="<?=$linkEncerrar?>" <?=$disabled?> class="btn-acao-excluir btn btn-danger">Encerrar</a>
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
