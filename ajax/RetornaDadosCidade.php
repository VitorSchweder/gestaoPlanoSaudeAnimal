<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

$retorno = array('logradouro' => null, 'bairro' => null, 'localidade' => null, 'uf' => null);

if(isset($_POST['cep'])) {
    $cep = filter_var($_POST['cep'], FILTER_SANITIZE_STRING);

    if(strpos($cep, '-') !== false) {
        $auxCep = explode('-', $cep);
        $cep = $auxCep[0].$auxCep[1];
    }

    $urlWebService = 'https://viacep.com.br/ws/'.$cep.'/json/';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $urlWebService);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $json = curl_exec($ch);

    $resultado = json_decode($json);

    curl_close($ch);

    if(isset($resultado->logradouro)) {
        $retorno['logradouro'] = $resultado->logradouro;
        $retorno['bairro'] = $resultado->bairro;
        $retorno['localidade'] = $resultado->localidade;
        $retorno['uf'] = $resultado->uf;
    }

    echo json_encode($retorno);
}
