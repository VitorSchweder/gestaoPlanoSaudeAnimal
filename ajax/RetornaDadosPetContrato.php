<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');
require_once(Constante::DIRETORIO_ROOT.'/include/ConexaoPdo.php');

$conexao = ConexaoPdo::getConexao();

if(isset($_POST['codigoPessoa'])) {
    $codigoPessoa = filter_var($_POST['codigoPessoa'], FILTER_SANITIZE_NUMBER_INT);

    $sqlPetsPessoa = 'SELECT codigo, nome
                        FROM pet
                       WHERE codigo_pessoa = :codigo_pessoa
                         AND codigo NOT IN(SELECT codigo_pet
                                             FROM pet_contrato
                                             JOIN pet
                                               ON pet.codigo = pet_contrato.codigo_pet
                                             JOIN contrato
                                               ON contrato.codigo = pet_contrato.codigo_contrato
                                            WHERE pet.codigo_pessoa = '.$codigoPessoa.'
                                              AND contrato.data_encerramento IS NULL)';

    $stmtPetsPessoa = $conexao->prepare($sqlPetsPessoa);
    $stmtPetsPessoa->bindValue(':codigo_pessoa', $codigoPessoa);
    $stmtPetsPessoa->execute();

    $htmlSelect = null;
    while($linha = $stmtPetsPessoa->fetch(PDO::FETCH_OBJ)) {
        $htmlSelect .= '<div class="container-grid">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <select class="form-control" name="pet[]"><option value="'.$linha->codigo.'">'.$linha->nome.'</option></select>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <a class="btn btn-danger btn-del">-</a>
                                </div>
                            </div>
                        </div>';
    }

    echo $htmlSelect;
}
