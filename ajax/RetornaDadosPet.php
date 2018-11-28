<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');
require_once(Constante::DIRETORIO_ROOT.'/include/ConexaoPdo.php');

$conexao = ConexaoPdo::getConexao();

if(isset($_POST['codigoPessoa'])) {
    $codigoPessoa = filter_var($_POST['codigoPessoa'], FILTER_SANITIZE_STRING);

    $sqlVerificaAtraso = 'SELECT (SELECT parcela.data_parcela
                                    FROM parcela
                                   WHERE codigo_contrato = contrato.codigo
                                     AND situacao = 1
                                ORDER BY data_parcela LIMIT 1) as data_parcela
                            FROM contrato
                            JOIN pessoa
                              ON pessoa.codigo = contrato.codigo_pessoa
                           WHERE pessoa.codigo = :codigo';
    $stmtVerificaAtraso = $conexao->prepare($sqlVerificaAtraso);
    $stmtVerificaAtraso->bindValue(':codigo', $codigoPessoa);
    $stmtVerificaAtraso->execute();

    $dataAtual = date('Y-m-d');
    $htmlSituacao = '<option class="hidden" id="situacao">1</option>';
    while($linhaAtraso = $stmtVerificaAtraso->fetch(PDO::FETCH_OBJ)) {
        $dataParcela = $linhaAtraso->data_parcela;

        if($dataAtual > $dataParcela) {
            $htmlSituacao = '<option class="hidden" id="situacao">0</option>';
        }
    }

    $sqlPet = 'SELECT codigo,
                      nome
                 FROM pet
                 JOIN pet_contrato
                   On pet_contrato.codigo_pet = pet.codigo
                WHERE codigo_pessoa = :codigo_pessoa';
    $stmtPet = $conexao->prepare($sqlPet);
    $stmtPet->bindValue(':codigo_pessoa', $codigoPessoa);
    $stmtPet->execute();

    $htmlLista = null;
    while($linha = $stmtPet->fetch(PDO::FETCH_OBJ)) {
        $htmlLista .= '<option value="'.$linha->codigo.'">'.$linha->nome.'</option>';
    }

    echo $htmlLista.$htmlSituacao;
}
