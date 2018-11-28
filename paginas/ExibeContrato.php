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
                                pessoa.cpf,
                                pessoa.logradouro,
                                pessoa.numero,
                                pessoa.bairro,
                                pessoa.cidade,
                                pessoa.estado,
                                plano.nome as nome_plano,
                                plano.valor as valor_plano,
                                plano.valor * 12 as valor_plano_anual,
                                contrato.codigo as codigo_contrato,
                                contrato.dia_primeira_parcela,
                                date(contrato.data) as data,
                                (SELECT COUNT(codigo_pet) FROM pet_contrato WHERE pet_contrato.codigo_contrato = contrato.codigo) AS total_pets,
                                plano.limite_pet
                           FROM contrato
                           JOIN pessoa
                             ON pessoa.codigo = contrato.codigo_pessoa
                           JOIN plano
                             ON plano.codigo = contrato.codigo_plano
                          WHERE contrato.codigo = :codigo';
$stmtDadosContrato = $conexao->prepare($sqlDadosContrato);
$stmtDadosContrato->bindValue(':codigo', $codigoContrato);
$stmtDadosContrato->execute();

while($linha = $stmtDadosContrato->fetch(PDO::FETCH_OBJ)) {
    $nomePessoa = $linha->nome_pessoa;
    $cpf = $linha->cpf;
    $logradouro = $linha->logradouro;
    $numero = $linha->numero;
    $bairro = $linha->bairro;
    $cidade = $linha->cidade;
    $estado = $linha->estado;
    $nomePlano = $linha->nome_plano;
    $valorPlano = $linha->valor_plano;
    $valorPlanoAnual = $linha->valor_plano_anual;
    $diaPrimeiraParcela = $linha->dia_primeira_parcela;
    $dataContrato = explode('-',$linha->data);
    $totalPets = $linha->total_pets;
    $limitePets = $linha->limite_pet;
}

if($totalPets > $limitePets) {
    $diferencaPets = $totalPets - $limitePets;
    $totalAumentarPercentual = $diferencaPets * 20; // aumenta 20% a cada pet acima do plano

    $valorPlano = $valorPlano + ($valorPlano * $totalAumentarPercentual) / 100;
    $valorPlanoAnual = $valorPlano * 12;
}

$valorPlanoAnualExtenso = str_replace('.','',$valorPlanoAnual) / 100;
$valorPlanoExtenso = str_replace('.','',$valorPlano) / 100;

$valorPlanoAnual = number_format($valorPlanoAnual, 2, ',', '.');
$valorPlano = number_format($valorPlano, 2, ',', '.');

$sqlProcedimentosContrato = 'SELECT procedimento.nome,
                                    case when peso = 0
                                        then \'Indiferente\'
                                        when peso = 1
                                            then \'Até 5kg\'
                                        when peso = 2
                                            then \'De 5 a 10kg\'
                                        when peso = 3
                                            then \'De 10 a 20kg\'
                                        when peso = 4
                                            then \'Acima de 20kg\'
                                    end as peso,
                                    procedimento.valor as valor_normal,
                                    procedimento_plano.valor_procedimento as valor_com_plano
                               FROM contrato
                               JOIN plano
                                 ON plano.codigo = contrato.codigo_plano
                               JOIN procedimento_plano
                                 ON procedimento_plano.codigo_plano = plano.codigo
                               JOIN procedimento
                                 ON procedimento.codigo = procedimento_plano.codigo_procedimento
                              WHERE contrato.codigo = :codigo';
$stmtDadosProcedimentoContrato = $conexao->prepare($sqlProcedimentosContrato);
$stmtDadosProcedimentoContrato->bindValue(':codigo', $codigoContrato);
$stmtDadosProcedimentoContrato->execute();

$mesExtenso = array(
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Marco',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Novembro',
        '10' => 'Setembro',
        '11' => 'Outubro',
        '12' => 'Dezembro'
    );
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12">
                        <h1><i class="fa fa-file-text"></i> CONTRATO DE PLANO DE SAÚDE ANIMAL <br /><small>(<strong><?=$nomePlano?></strong> – Contrato N° <strong><?=$codigoContrato?></strong>)</small></h1>
                        <a class="no-print btn-acao-alterar btn btn-info" href="javascript:if(window.print)window.print()"><i class="fa fa-print"></i> Imprimir</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 contrato">

                    <p>Contrato de cobertura de custos dos procedimentos de assistência médica veterinária, que entre si fazem de um lado a <strong>CLINICA VETERINARIA AQUARIOS BEL LTDA</strong>, CNPJ <strong>14.703.288/0001-75</strong>, situada a Rua XV de Novembro, Centro, 209, Sala 03 na cidade de Rio do Sul, SC denominada <strong>CONTRATADA</strong>, do outro lado o signatário do presente contrato, <strong><?=$nomePessoa?></strong>, CPF <strong><?=$cpf?></strong>, situado a <strong><?=$logradouro?></strong>, <strong><?=$numero?></strong>, <strong><?=$bairro?></strong>, na cidade de <strong><?=$cidade?></strong>, <strong><?=$estado?></strong> doravante denominado <strong>CONTRATANTE</strong> e de outro lado o(s) animal(ais) constado(s) na(s) ficha(s) de adesão (em um total de <strong><?=$totalPets?> Pet(s)</strong>) em anexo ao contrato, (devidamente assinada pelo <strong>CONTRATANTE</strong>), denominado <strong>BENEFICIÁRIO</strong>.</p>

                    <p>CLÁUSULA PRIMEIRA. O presente contrato tem por objetivo a cobertura de custos dos serviços de assistência médica veterinária na forma <strong>CONTRATADA</strong>, optando pelo plano oferecido. Em anexo ao contrato o formulário apresenta as opções/serviços contratados entre as partes.</p>

                    <p>CLÁUSULA SEGUNDA. Ao adquirir o plano, o <strong>CONTRATANTE</strong> terá direito aos serviços discriminados no anexo (referente ao plano escolhido) ao <strong>BENEFICIÁRIO</strong>, indicado na ficha em anexo, que fica fazendo parte do presente contrato.</p>

                    <p>CLÁUSULA TERCEIRA. O <strong>BENEFICIÁRIO</strong> terá direito somente aos serviços optados no plano em anexo, sendo que o que não estiver previsto, de forma expressa, automaticamente estará excluído do atendimento.</p>

                    <p>CLÁUSULA QUARTA. É de obrigação do <strong>CONTRATANTE</strong> pagar, de acordo com a estabelecida <strong>CONTRATADA</strong>, relativamente ao local, forma e data de pagamento, a mensalidade.</p>

                    <p>CLÁUSULA QUINTA. Das Consultas e Coberturas. As consultas simples e especializadas serão realizadas de segunda-feira à sexta-feira, das 8:00h às 12:00h e das 13:00h às 18:00h no endereço da <strong>CONTRATADA</strong>, e deverão ser agendadas por telefone. As consultas de emergência/plantão (fora de horário comercial) terão um custo de R$ 200,00 (duzentos reais), conforme a cláusula nona.</p>

                    <p>CLÁUSULA SEXTA. Dos Procedimentos Clínicos e Coberturas. Os planos contratados têm coberturas para vários procedimentos clínicos, de acordo com a modalidade em questão.</p>

                    <p>CLÁUSULA SÉTIMA. Das Imunizações (vacinas) e coberturas. Todos os planos possuem desconto de 5% (cinco por cento) na aquisição e aplicação das vacinas. A <strong>CONTRATADA</strong> após a contratação poderá monitorar as vacinações do <strong>BENEFICIÁRIO</strong>.</p>

                    <p>CLÁUSULA OITAVA. Das Cirurgias e Coberturas. As cirurgias necessárias serão executadas de acordo com a cobertura <strong>CONTRATADA</strong>. Todos os procedimentos deverão ser agendados previamente pelo <strong>CONTRATANTE</strong> com a <strong>CONTRATADA</strong>, salvo emergências. O não cumprimento dos horários e datas agendadas será considerado como desistência por parte da <strong>CONTRATANTE</strong> e o paciente não poderá realizar o procedimento até a próxima vigência (30 dias). As cirurgias eletivas, como castração, poderão ser reagendadas devido ao encaixe de cirurgias de emergência. Todas as cirurgias terão uma taxa de pós operatório que não será coberta pelos planos com um valor diferenciado. Neste serviço será avaliado o controle de dor e serão feitas as aplicações de antibiótico, analgésico e anti-inflamatório. O <strong>BENEFICIÁRIO</strong> será liberado após a autorização do médico veterinário responsável.</p>

                    <p>CLÁUSULA NONA. Atendimento de Emergência: O plano disponibiliza a consulta emergencial no valor de R$ 200,00 (duzentos reais).</p>

                    <p>CLÁUSULA DÉCIMA. SERVIÇO DE INTERNAÇÃO E UTI. O serviço de internação e UTI varia de acordo o plano contratado.</p>

                    <p>CLÁUSULA DÉCIMA PRIMEIRA. Limites de <strong>BENEFICIÁRIOS</strong> do plano.  No Plano Light o <strong>CONTRATANTE</strong> poderá incluir no máximo 1 (um) <strong>BENEFICIÁRIO</strong> ao contrato. No Plano Plus o <strong>CONTRATANTE</strong> poderá incluir até 2 (dois) <strong>BENEFICIÁRIO</strong>S ao contrato sem custo adicional. Em caso de inclusão acima de 2 (dois) <strong>BENEFICIÁRIO</strong>S no Plano Plus, será acrescido ao valor do contrato 20% (vinte por cento) por cada <strong>BENEFICIÁRIO</strong> incluído. Serão permitidos no máximo 4 (quatro) <strong>BENEFICIÁRIO</strong>S por contrato.</p>

                    <p>CLÁUSULA DÉCIMA SEGUNDA.  Dos prazos de vigência: O contrato terá vigência de 12 (doze) meses a contar da data de assinatura e terá renovação anual automática sem recolhimento de taxa de adesão. O <strong>CONTRATANTE</strong> poderá requerer o cancelamento do plano mediante requerimento escrito, com antecedência mínima de 30 (trinta) dias. Recai sobre o <strong>CONTRATANTE</strong> a obrigação de adimplir com todas as parcelas vincendas pactuadas na contratação. Na eventualidade de débitos existentes, quando do pedido de cancelamento, o <strong>CONTRATANTE</strong> deverá pagá-los, mesmo depois de extinto o contrato. Caso não faça corre o risco de ser executado judicialmente assumindo que este contrato será um título líquido, certo e exigível. Para manter o equilíbrio contratual entre as partes, o presente contrato tem duração mínima de 12 (doze) meses.</p>

                    <p>CLÁUSULA DÉCIMA TERCEIRA. Do valor e reajuste das mensalidades. O valor do <strong><?=$nomePlano?></strong> terá o custo de <strong>R$ <?=$valorPlanoAnual?></strong> (<?=converteValorExtenso($valorPlanoAnualExtenso)?>) ao ano parcelado em 12 (doze) mensalidades no valor de <strong>R$ <?=$valorPlano?></strong> (<?=converteValorExtenso($valorPlanoExtenso)?>) à vencer no dia <strong><?=$diaPrimeiraParcela?></strong> (<?=retornaDiaExtenso($diaPrimeiraParcela)?>) de cada mês. O plano de saúde contratado pelo <strong>CONTRATANTE</strong> poderá ser reajustado anualmente, de acordo com a variação do IGPM da Fundação Getúlio Vargas ou outro índice que vier a substituí-lo, bem como os procedimentos cirúrgicos, medicamentos previstos na tabela especificada em anexo com os descontos ofertados.</p>

                    <p>CLÁUSULA DÉCIMA QUARTA. Das obrigações do <strong>CONTRATANTE</strong> para com seu animal. O <strong>CONTRATANTE</strong> denominado de proprietário deverá manter seu animal com as mínimas condições de vida, sendo responsável pela oferta de alimento (ração), água, exercícios, abrigo das intempéries e higiene; O <strong>CONTRATANTE</strong> deverá seguir as recomendações e prescrições realizadas pela equipe de médicos veterinários da <strong>CONTRATADA</strong> e das clínicas credenciadas de forma integral; O não cumprimento das recomendações e prescrições caracteriza negligência, que por sua vez poderá ocasionar falha no tratamento e risco para a integridade do animal, uma vez caracterizada negligência por parte do proprietário, exime-se a responsabilidade por parte da <strong>CONTRATADA</strong> e deste contrato;</p>

                    <p>CLÁUSULA DÉCIMA QUINTA. Da Inclusão e Exclusão de animais. A inclusão ou a exclusão de <strong>BENEFICIÁRIO</strong> deverá ser realizada junto o representante da <strong>CONTRATADA</strong>. A formalização do procedimento será feita pela ficha de adesão por meio do preenchimento dos itens referentes à movimentação do animal em questão. Substituição de <strong>BENEFICIÁRIO</strong>: Caso o <strong>CONTRATANTE</strong> queira substituir o paciente, será realizado o check-up padrão pela <strong>CONTRATADA</strong>. Inclusão de novos animais: Caso o <strong>CONTRATANTE</strong> queira incluir um novo <strong>BENEFICIÁRIO</strong>, será realizado o check-up padrão pela <strong>CONTRATADA</strong>. Exclusão de animais: Caso o <strong>CONTRATANTE</strong> deseje retirar ou cancelar o plano de saúde para o <strong>BENEFICIÁRIO</strong> com cobertura, deverá formalizar a solicitação, oficiando a exclusão junto a <strong>CONTRATADA</strong>. Também neste caso, deverá ser preenchida a planilha de adesão. Migração de plano: É permitido o <strong>CONTRATANTE</strong> realizar a migração do plano ajustando os valores da mensalidade e permanecendo no mínimo 6 (seis) meses, caso queira retornar ao plano anterior contratado.</p>

                    <p>CLÁUSULA DÉCIMA SEXTA. Todos os procedimentos não cobertos pelo plano terão um valor diferenciado junto a <strong>CONTRATADA</strong> (excetos os exames encaminhados para laboratórios de terceiros), caso o <strong>CONTRATANTE</strong> necessite utilizar.</p>

                    <p>CLÁUSULA DÉCIMA SÉTIMA. Faz parte deste contrato uma declaração de saúde e descrição minuciosa do <strong>BENEFICIÁRIO</strong>, incluindo idade, porte, raça, pelagem, características, sexo. Se qualquer destas informações for falsa, será motivo de rescisão contratual, sem direito a nenhuma espécie de reembolso.</p>

                    <p>CLÁUSULA DÉCIMA OITAVA. Questões omissas ou ainda não reconhecidas pela legislação brasileira serão sanadas, oportunamente, com preferência à transação extrajudicial, podendo, ainda, o plano contratado ceder, vender ou etc., sua relação comercial a terceiro.</p>

                    <p>CLÁUSULA DÉCIMA NONA. Fica eleito o Foro da cidade de Rio do Sul - SC, para caso de pendência judicial ou litígio, renunciando a qualquer outro, por mais privilegiado que seja.</p>

                    <p>E, por estarem justos e acertados, firmam o presente instrumento em 2 (duas) vias de igual forma e teor, na presença de duas testemunhas.</p>

                    <p>Rio do Sul, <?=$dataContrato[2]?> de <?=$mesExtenso[$dataContrato[1]]?> de <?=$dataContrato[0]?>.</p>
                    <br><br><br>

                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <p>
                        ________________________________________________
                        <br>
                        Clinica Veterinaria Aquarios Bel Ltda (Planbel)<br>
                        CNPJ 14.703.288/0001-75
                        <br><br><br><br>
                    </p>
                    <p>
                        ________________________________________________
                        <br>
                        <?=$nomePessoa?><br>
                        CPF <?=$cpf?>
                        <br><br><br><br>
                    </p>
                </div>
                <div class="col-md-5">
                    <p>
                        ________________________________________________
                        <br>
                        Testemunha
                        <br><br><br><br>
                    </p>
                    <p>
                        <br>
                        ________________________________________________
                        <br>
                        Testemunha<br>
                    </p>
                    <br><br><br>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <h3>Valores dos Procedimentos</h3>
                    <hr/>
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Procedimento</th>
                                            <th>Peso</th>
                                            <th>Valor sem Plano</th>
                                            <th>Valor <?=$nomePlano?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    while($linha = $stmtDadosProcedimentoContrato->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                        <tr>
                                            <td><?=$linha->nome?></td>
                                            <td><?=$linha->peso?></td>
                                            <td>R$ <?=number_format($linha->valor_normal, 2,',','.')?></td>
                                            <td>R$ <?=number_format($linha->valor_com_plano, 2,',','.')?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>
        </div>
    </div>
</div>
