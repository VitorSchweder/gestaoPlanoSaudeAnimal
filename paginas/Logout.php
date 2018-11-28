<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

unset($_SESSION['ID_USUARIO']);
unset($_SESSION['EMAIL_USUARIO']);
unset($_SESSION['TIPO_USUARIO']);
unset($_SESSION['NOME_USUARIO']);

executaJs('location.href="'.Constante::LINK.'/login"');
exit;
