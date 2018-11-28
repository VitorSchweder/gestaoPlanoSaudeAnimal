<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

$mensagemErro = null;
$mensagem = null;
if(isset($_POST['senha'])) {
    $senha = filter_var(trim($_POST['senha']), FILTER_SANITIZE_STRING);
    $confirmarSenha = filter_var(trim($_POST['confirma-senha']), FILTER_SANITIZE_STRING);

    if($senha != $confirmarSenha) {
        $mensagemErro = 'As duas senhas devem ser iguais';
    }
    else if(strlen($senha) < 6) {
        $mensagemErro = 'A senha deve ter no mínimo 6 caracteres';
    }
    else {
        $senha = password_hash($senha, PASSWORD_DEFAULT);

        $sqlSenha = 'UPDATE pessoa SET senha = :senha WHERE codigo = :codigo';
        $stmtSenha = $conexao->prepare($sqlSenha);
        $stmtSenha->bindValue(':senha', $senha);
        $stmtSenha->bindValue(':codigo', $_SESSION['ID_USUARIO']);
        $stmtSenha->execute();

        $mensagem = 'Senha alterada com sucesso';
    }
}

if(!empty($acaoPagina)) {
    include(Constante::DIRETORIO_ROOT.'/paginas/404.php');
}
else {
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
             <div class="page-header">
                <h1><i class="fa fa-key"></i> Alterar <small>Senha</small></h1>
            </div>
            <?php
            if(!empty($mensagemErro)) {
            ?>
                <div class="row margem-inferior-10">
                    <div class="col-xs-5">
                        <div class="alert alert-danger">
                            <?=$mensagemErro?>
                        </div>
                    </div>
                </div>
            <?php
            }
            else if(!empty($mensagem)) {
            ?>
                <div class="row margem-inferior-10">
                    <div class="col-xs-5">
                        <div class="alert alert-success">
                            <?=$mensagem?>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
            <form role="form" action="<?=Constante::LINK.'/alterarSenha'?>" method="post">
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                             <input type="password" class="form-control" name="senha" placeholder="Nova senha">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                             <input type="password" class="form-control" name="confirma-senha" placeholder="Confirmar senha">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <input type="submit" name="cadastrar" class="btn btn-primary" value="Alterar"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
}
