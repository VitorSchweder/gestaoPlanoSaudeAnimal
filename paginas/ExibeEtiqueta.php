<?php
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');

$urlContrato = $_SERVER['REQUEST_URI'];
$auxUrlContrato = explode('?', $urlContrato);
$codigoContrato = null;

if(isset($auxUrlContrato[1])) {
    $codigoContrato = explode('=', $auxUrlContrato[1]);
    $codigoContrato = $codigoContrato[1];
}

$sqlDadosContrato = 'SELECT pessoa.nome as nome_pessoa,
                            plano.nome as nome_plano,
                            contrato.codigo as codigo_contrato,
                            contrato.validade
                       FROM contrato
                       JOIN pessoa
                         ON pessoa.codigo = contrato.codigo_pessoa
                       JOIN plano
                         ON plano.codigo = contrato.codigo_plano
                      WHERE contrato.codigo = :codigo';
$stmtDadosContrato = $conexao->prepare($sqlDadosContrato);
$stmtDadosContrato->bindValue(':codigo', $codigoContrato);
$stmtDadosContrato->execute();
?>
<div id="page-wrapper">
    <div class="row">

        <div class="col-md-12">
            <style>

                .etiqueta{
                    border: 1px solid #9c9c9c;
                    height: 95px;
                    margin: 3px;
                    width: 32%;
                    padding: 5px;
                    float: left;
                    font-size: 14px;
                    text-align: center;
                }
                #etiquetas{
                    width: 100%;
                }

            </style>
            <table id="etiquetas">
                <tr>
                    <td class="etiqueta ativa">
                        <?php
                        while($linha = $stmtDadosContrato->fetch(PDO::FETCH_OBJ)) {
                            $nomePessoa = $linha->nome_pessoa;
                            $nomePlano = $linha->nome_plano;
                            $codigoContrato = $linha->codigo_contrato;
                            $validadeContrato = explode('-', $linha->validade);
                            $validadeContrato = $validadeContrato[2].'/'.$validadeContrato[1].'/'.$validadeContrato[0];

                            $petContrato = [];
                            $sqlPet = 'SELECT pet.nome
                                      FROM pet
                                      JOIN pet_contrato
                                        ON pet_contrato.codigo_pet = pet.codigo
                                     WHERE pet_contrato.codigo_contrato = :codigo_contrato';
                            $stmtDadosPet = $conexao->prepare($sqlPet);
                            $stmtDadosPet->bindValue(':codigo_contrato', $codigoContrato);
                            $stmtDadosPet->execute();
                            while($linhaPet = $stmtDadosPet->fetch(PDO::FETCH_OBJ)) {
                                $petContrato[] = $linhaPet->nome;
                            }

                        ?>
                        <strong>Cliente:</strong> <?=$nomePessoa?><br />
                        <strong>Contrato:</strong> <?=$codigoContrato?><br />
                        <strong>Pet(s):</strong> <?=implode(', ',$petContrato)?><br />
                        <strong><?=$nomePlano?></strong> (Val: <?=$validadeContrato?>)
                        <?php
                        }
                        ?>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <!--
<div id="etiquetas">
<div class="etiqueta ativa">
<?php
while($linha = $stmtDadosContrato->fetch(PDO::FETCH_OBJ)) {
    $nomePessoa = $linha->nome_pessoa;
    $nomePlano = $linha->nome_plano;
    $codigoContrato = $linha->codigo_contrato;
    $validadeContrato = explode('-', $linha->validade);
    $validadeContrato = $validadeContrato[2].'/'.$validadeContrato[1].'/'.$validadeContrato[0];

    $petContrato = [];
    $sqlPet = 'SElECT pet.nome
                                      FROM pet
                                      JOIN pet_contrato
                                        ON pet_contrato.codigo_pet = pet.codigo
                                     WHERE pet_contrato.codigo_contrato = :codigo_contrato';
    $stmtDadosPet = $conexao->prepare($sqlPet);
    $stmtDadosPet->bindValue(':codigo_contrato', $codigoContrato);
    $stmtDadosPet->execute();
    while($linhaPet = $stmtDadosPet->fetch(PDO::FETCH_OBJ)) {
        $petContrato[] = $linhaPet->nome;
    }

?>
<strong>Cliente:</strong> <?=$nomePessoa?><br />
<strong>Contrato:</strong> <?=$codigoContrato?><br />
<strong>Pet(s):</strong> <?=implode(', ',$petContrato)?><br />
<strong><?=$nomePlano?></strong> (Val: <?=$validadeContrato?>)
<?php
}
?>
</div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
<div class="etiqueta"></div>
</div>
-->
        </div>
    </div>
</div>
