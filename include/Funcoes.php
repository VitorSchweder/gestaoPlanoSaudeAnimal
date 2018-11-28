<?php
function isUsuarioCliente() {
    return $_SESSION['TIPO_USUARIO'] == Constante::TIPO_USUARIO_CLIENTE;
}

function isUsuarioAdministrador() {
    return $_SESSION['TIPO_USUARIO'] == Constante::TIPO_USUARIO_ADMINISTRADOR;
}

function isUsuarioVeterinario() {
    return $_SESSION['TIPO_USUARIO'] == Constante::TIPO_USUARIO_VETERINARIO;
}

function executaJs($parametro) {
    echo "<script>".$parametro."</script>";
}

function converteValorExtenso($number) {
    $hyphen      = '-';
    $conjunction = ' e ';
    $separator   = ', ';
    $negative    = 'menos ';
    $decimal     = ' ponto ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'um',
        2                   => 'dois',
        3                   => 'três',
        4                   => 'quatro',
        5                   => 'cinco',
        6                   => 'seis',
        7                   => 'sete',
        8                   => 'oito',
        9                   => 'nove',
        10                  => 'dez',
        11                  => 'onze',
        12                  => 'doze',
        13                  => 'treze',
        14                  => 'quatorze',
        15                  => 'quinze',
        16                  => 'dezesseis',
        17                  => 'dezessete',
        18                  => 'dezoito',
        19                  => 'dezenove',
        20                  => 'vinte',
        30                  => 'trinta',
        40                  => 'quarenta',
        50                  => 'cinquenta',
        60                  => 'sessenta',
        70                  => 'setenta',
        80                  => 'oitenta',
        90                  => 'noventa',
        100                 => 'cento',
        200                 => 'duzentos',
        300                 => 'trezentos',
        400                 => 'quatrocentos',
        500                 => 'quinhentos',
        600                 => 'seiscentos',
        700                 => 'setecentos',
        800                 => 'oitocentos',
        900                 => 'novecentos',
        1000                => 'mil',
        1000000             => array('milhão', 'milhões'),
        1000000000          => array('bilhão', 'bilhões'),
        1000000000000       => array('trilhão', 'trilhões'),
        1000000000000000    => array('quatrilhão', 'quatrilhões'),
        1000000000000000000 => array('quinquilhão', 'quinquilhões')
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'converteValorExtenso só aceita números entre ' . PHP_INT_MAX . ' à ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . converteValorExtenso(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $conjunction . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = floor($number / 100)*100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds];
            if ($remainder) {
                $string .= $conjunction . converteValorExtenso($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            if ($baseUnit == 1000) {
                $string = converteValorExtenso($numBaseUnits) . ' ' . $dictionary[1000];
            } elseif ($numBaseUnits == 1) {
                $string = converteValorExtenso($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
            } else {
                $string = converteValorExtenso($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
            }
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= converteValorExtenso($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function retornaDiaExtenso($dia) {
    $diaExtenso = [1 => 'um', 2 => 'dois', 3 => 'três', 4 => 'quatro', 5 => 'cinco', 6 => 'seis', 7 => 'sete', 8 => 'oito', 9 => 'nove', 10 => 'dez', 11 => 'onze', 12 => 'doze', 13 => 'treze', 14 => 'quatorze', 15 => 'quinze',
                   16 => 'dezesseis', 17 => 'dezessete', 18 => 'dezoito', 19 => 'dezenove', 20 => 'vinte', 21 => 'vinte e um', 22 => 'vinte e dois', 23 => 'vinte e três', 24 => 'vinte e quatro', 25 => 'vinte e cinco', 26 => 'vinte e seis', 27 => 'vinte e sete', 28 => 'vinte e oito', 29 => 'vinte e nove', 30 => 'trinta', 31 => 'trinta e um'];

    return $diaExtenso[$dia];
}

function gravaMoeda($get_valor) {
    $source = array('.', ',');
    $replace = array('', '.');
    $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
    return $valor; //retorna o valor formatado para gravar no banco
}

function exibeData($get_valor) {
    $data = explode('-', $get_valor);
    $data = $data[2].'/'.$data[1].'/'.$data[0];
    return $data;
}

function calcularParcelas($numeroParcelas, $dataPrimeiraParcela = null, $codigoContrato = null, $codigoPlano = null, $conexao = null, $tipoAcao = 'incluir', $totalPets = 0) {
    if($dataPrimeiraParcela != null){
        $dataPrimeiraParcela = explode( "/",$dataPrimeiraParcela);
        $dia = $dataPrimeiraParcela[0];
        $mes = $dataPrimeiraParcela[1];
        $ano = $dataPrimeiraParcela[2];
    }
    else {
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
    }

    $sqlDadosPlano = 'SELECT plano.valor,
                             plano.limite_pet
                        FROM plano WHERE plano.codigo = :codigo';
    $stmtDadosPlano = $conexao->prepare($sqlDadosPlano);
    $stmtDadosPlano->bindValue(':codigo', $codigoPlano);
    $stmtDadosPlano->execute();

    $valorPlano = null;
    while($linha = $stmtDadosPlano->fetch(PDO::FETCH_OBJ)) {
        $valorParcela = $linha->valor;
        $limitePets = $linha->limite_pet;
    }

    if($totalPets > $limitePets) {
        $diferencaPets = $totalPets - $limitePets;
        $totalAumentarPercentual = $diferencaPets * 20; // aumenta 20% a cada pet acima do plano
    
        $valorParcela = $valorParcela + ($valorParcela * $totalAumentarPercentual) / 100;
    }

    if($tipoAcao == 'incluir') {
        $contadorParcela = 1;
        for($x = 0; $x < $numeroParcelas; $x++){
            $dataParcela = date("Y-m-d",strtotime("+".$x." month",mktime(0, 0, 0,$mes,$dia,$ano)));

            $sqlInsereParcela = 'INSERT INTO parcela (valor_parcela, data_parcela, codigo_contrato, numero_parcela)
                                               VALUES(:valor_parcela, :data_parcela, :codigo_contrato, :numero_parcela)';
            $stmtInsereParcela = $conexao->prepare($sqlInsereParcela);
            $stmtInsereParcela->bindValue(':valor_parcela', $valorParcela);
            $stmtInsereParcela->bindValue(':data_parcela', $dataParcela);
            $stmtInsereParcela->bindValue(':codigo_contrato', $codigoContrato);
            $stmtInsereParcela->bindValue(':numero_parcela', $contadorParcela);
            $stmtInsereParcela->execute();

            $contadorParcela++;
        }
    }
    else {
        for($x = 1; $x < $numeroParcelas; $x++){
            $sqlAtualizaParcela = 'UPDATE parcela
                                      SET valor_parcela = :valor_parcela
                                    WHERE codigo_contrato = :codigo_contrato
                                      AND numero_parcela = :numero_parcela';
            $stmtAtualizaParcela = $conexao->prepare($sqlAtualizaParcela);
            $stmtAtualizaParcela->bindValue(':valor_parcela', $valorParcela);
            $stmtAtualizaParcela->bindValue(':codigo_contrato', $codigoContrato);
            $stmtAtualizaParcela->bindValue(':numero_parcela', $x);
            $stmtAtualizaParcela->execute();
        }
    }
}
