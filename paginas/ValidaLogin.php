<?php
if (!isset($_POST['cpf']) || !isset($_POST['senha'])) {
     executaJs('location.href="'.Constante::LINK.'/login"');
     exit;
}

$cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_STRING);
$cpf = str_replace('.', '', $cpf);
$cpf = str_replace('-', '', $cpf);
$senha = filter_var($_POST['senha'], FILTER_SANITIZE_STRING);

$sql = 'SELECT codigo,
               nome,
               email,
               tipo,
               senha
          FROM pessoa
         WHERE cpf = :cpf
           AND possui_acesso = 1';
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':cpf', $cpf);
$stmt->execute();

if ($stmt->rowCount() < 1) {
    $_SESSION['ERRO_LOGIN'] = true;
    executaJs('location.href="'.Constante::LINK.'/login"');
     exit;}
else {
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    while($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
        if(password_verify($senha, $linha->senha)) {
            $_SESSION['ID_USUARIO'] = $linha->codigo;
            $_SESSION['EMAIL_USUARIO'] = $linha->email;
            $_SESSION['TIPO_USUARIO'] = $linha->tipo;
            $_SESSION['NOME_USUARIO'] = $linha->nome;
	                
            executaJs('location.href="'.Constante::LINK.'/home"');
            exit;
        }
        else {
            unset($_SESSION['ID_USUARIO']);
            unset($_SESSION['EMAIL_USUARIO']);
            unset($_SESSION['TIPO_USUARIO']);
            unset($_SESSION['NOME_USUARIO']);

            $_SESSION['ERRO_LOGIN'] = true;
            executaJs('location.href="'.Constante::LINK.'/login"');
     exit;

        }
    }

    executaJs('location.href="'.Constante::LINK.'/home"');
     exit;

}
