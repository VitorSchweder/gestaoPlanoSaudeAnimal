<?php
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if(isset($_SESSION['ID_USUARIO'])) {
        executaJs('location.href="'.Constante::LINK.'/home"');
        exit;
    }
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default text-center">
            <img src="http://planbel.com.br/img/logo.png" style="width:80%;margin:12px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Área do Cliente</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="<?=Constante::LINK.'/validaLogin'?>" method="post" id="login">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" autocomplete="off" placeholder="CPF" name="cpf" type="cpf" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" autocomplete="off" placeholder="Senha" name="senha" type="password" value="">
                            </div>
                            <button type="submit" class="btn btn-lg btn-success btn-block" style="background-color:#60b2ae !important;border:0 !important;">Login</button>
                        </fieldset>
                    </form>
                    <?php
                        if(isset($_SESSION['ERRO_LOGIN'])) {
                            echo '<div class="alert alert-danger" style="text-align:center;margin-top: 10px !important;padding: 0;">Login e/ou senha inválido(s)!</div>';
                            unset($_SESSION['ERRO_LOGIN']);
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
