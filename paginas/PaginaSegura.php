<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if(!isset($_SESSION['ID_USUARIO'])) {
    executaJs('location.href="'.Constante::LINK.'/login"');
    exit;
}
