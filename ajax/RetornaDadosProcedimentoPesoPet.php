<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');
require_once(Constante::DIRETORIO_ROOT.'/include/ConexaoPdo.php');

$conexao = ConexaoPdo::getConexao();

if(isset($_POST['codigo'])) {
    $codigoPet = filter_var($_POST['codigo'], FILTER_SANITIZE_NUMBER_INT);

    $sqlPesoPet = 'SELECT peso from pet where codigo = :codigo';
    $stmt = $conexao->prepare($sqlPesoPet);
    $stmt->bindValue(':codigo', $codigoPet);
    $stmt->execute();
    while($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
        $pesoPet = $linha->peso;
    }

    $sqlProcedimentos = 'SELECT codigo,
                                nome,
                                case when peso = 0
                                    then \'Não informado\'
                                    when peso = 1
                                        then \'Até 5kg\'
                                    when peso = 2
                                        then \'De 5 a 10kg\'
                                    when peso = 3
                                        then \'De 10 a 20kg\'
                                    when peso = 4
                                        then \'Acima de 20kg\'
                                end as peso
                           FROM procedimento
                          WHERE peso = '.$pesoPet.' OR peso = 0';
    $stmtProcedimentos = $conexao->prepare($sqlProcedimentos);
    $stmtProcedimentos->execute();

    $htmlLista = null;
    while($linha = $stmtProcedimentos->fetch(PDO::FETCH_OBJ)) {
        $htmlLista .= '<option value="'.$linha->codigo.'">'.$linha->nome.'</option>';
    }

    echo $htmlLista;
}
