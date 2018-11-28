<?php
if(isUsuarioCliente()) {
    header('Location:'.Constante::LINK.'/home');
    exit;
}

if(isset($_POST['nome'])) {
    $nome = filter_var(trim($_POST['nome']), FILTER_SANITIZE_STRING);
    $cpf = filter_var(trim($_POST['cpf']), FILTER_SANITIZE_STRING);
    $cpf = str_replace('.','',$cpf);
    $cpf = str_replace('-','',$cpf);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
    $cep = filter_var(trim($_POST['cep']), FILTER_SANITIZE_STRING);
    $cep = str_replace('-','',$cep);
    $logradouro = filter_var(trim($_POST['logradouro']), FILTER_SANITIZE_STRING);
    $numero = filter_var(trim($_POST['numero']), FILTER_SANITIZE_STRING);
    $complemento = filter_var(trim($_POST['complemento']), FILTER_SANITIZE_STRING);
    $bairro = filter_var(trim($_POST['bairro']), FILTER_SANITIZE_STRING);
    $cidade = filter_var(trim($_POST['cidade']), FILTER_SANITIZE_STRING);
    $uf = filter_var(trim($_POST['estado']), FILTER_SANITIZE_STRING);
    $tipo = filter_var(trim($_POST['tipo']), FILTER_SANITIZE_STRING);
    $possuiAcesso = isset($_POST['possui-acesso']) ? true : false;
    $possuiAcessoInserir = $possuiAcesso ? 1 : 0;
    $codigoPessoa = filter_var($_POST['codigo'], FILTER_SANITIZE_NUMBER_INT);

    /*
     * Insere
     */
    if(empty($codigoPessoa)) {
        $senha = null;
        if($possuiAcesso) {
            $senha = substr($cpf, 0, 6);
            $senha = password_hash($senha, PASSWORD_DEFAULT);
        }

        $sqlInserePessoa = 'INSERT INTO pessoa (nome, cpf, cep, logradouro, numero, complemento, bairro, cidade, estado, tipo, senha, email, possui_acesso)
                                        VALUES (:nome, :cpf, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado, :tipo, :senha, :email, :possui_acesso)';
        $stmtInserePessoa = $conexao->prepare($sqlInserePessoa);
        $stmtInserePessoa->bindValue(':nome', $nome);
        $stmtInserePessoa->bindValue(':cpf', $cpf);
        $stmtInserePessoa->bindValue(':cep', $cep);
        $stmtInserePessoa->bindValue(':logradouro', $logradouro);
        $stmtInserePessoa->bindValue(':numero', $numero);
        $stmtInserePessoa->bindValue(':complemento', $complemento);
        $stmtInserePessoa->bindValue(':bairro', $bairro);
        $stmtInserePessoa->bindValue(':cidade', $cidade);
        $stmtInserePessoa->bindValue(':estado', $uf);
        $stmtInserePessoa->bindValue(':tipo', $tipo);
        $stmtInserePessoa->bindValue(':email', $email);
        $stmtInserePessoa->bindValue(':senha', $senha);
        $stmtInserePessoa->bindValue(':possui_acesso', $possuiAcessoInserir);
        $stmtInserePessoa->execute();

        $sqlUltimaPessoa = 'SELECT MAX(codigo) as ultimo FROM pessoa';
        $stmtUltimaPessoa = $conexao->prepare($sqlUltimaPessoa);
        $stmtUltimaPessoa->execute();

        $ultimaPessoa = 0;
        while($linha = $stmtUltimaPessoa->fetch(PDO::FETCH_OBJ)) {
            $ultimaPessoa = $linha->ultimo;
        }

        $telefones = $_POST['telefone'];
        if(count($telefones) > 0) {
            foreach($telefones as $telefone) {
                $sqlInsereTelefone = 'INSERT INTO telefone (codigo_pessoa, numero) VALUES (:codigo_pessoa, :numero)';
                $stmtInsereTelefone = $conexao->prepare($sqlInsereTelefone);
                $stmtInsereTelefone->bindValue(':codigo_pessoa', $ultimaPessoa);
                $stmtInsereTelefone->bindValue(':numero', $telefone);
                $stmtInsereTelefone->execute();
            }
        }

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_INCLUIDO;
        executaJs('location.href="'.Constante::LINK.'/pessoa"');
        exit;
    }
    else {
        $senha = null;
        if($possuiAcesso) {
            $sqlSenha = 'SELECT senha FROM pessoa WHERE codigo = :codigo';
            $stmtSenha = $conexao->prepare($sqlSenha);
            $stmtSenha->bindValue(':codigo', $codigoPessoa);
            $stmtSenha->execute();

            $senhaVerificar = null;
            while($linha = $stmtSenha->fetch(PDO::FETCH_OBJ)) {
                $senhaVerificar = $linha->senha;
            }

            if(empty($senhaVerificar)) {
                $senha = substr($cpf, 0, 6);
                $senha = password_hash($senha, PASSWORD_DEFAULT);
            }
        }

        $sqlAtualizaPessoa = 'UPDATE pessoa
                                 SET nome = :nome,
                                     cpf = :cpf,
                                     cep = :cep,
                                     logradouro = :logradouro,
                                     numero = :numero,
                                     complemento = :complemento,
                                     bairro = :bairro,
                                     cidade = :cidade,
                                     estado = :estado,
                                     email = :email,
                                     tipo = :tipo,
                                     possui_acesso = :possui_acesso';

        if(!empty($senha)) {
            $sqlAtualizaPessoa .= ', senha = :senha';
        }

        $sqlAtualizaPessoa .= '
                               WHERE codigo = :codigo';

        $stmtAtualizaPessoa = $conexao->prepare($sqlAtualizaPessoa);
        $stmtAtualizaPessoa->bindValue(':codigo', $codigoPessoa);
        $stmtAtualizaPessoa->bindValue(':nome', $nome);
        $stmtAtualizaPessoa->bindValue(':cpf', $cpf);
        $stmtAtualizaPessoa->bindValue(':cep', $cep);
        $stmtAtualizaPessoa->bindValue(':logradouro', $logradouro);
        $stmtAtualizaPessoa->bindValue(':numero', $numero);
        $stmtAtualizaPessoa->bindValue(':complemento', $complemento);
        $stmtAtualizaPessoa->bindValue(':bairro', $bairro);
        $stmtAtualizaPessoa->bindValue(':cidade', $cidade);
        $stmtAtualizaPessoa->bindValue(':estado', $uf);
        $stmtAtualizaPessoa->bindValue(':tipo', $tipo);
        $stmtAtualizaPessoa->bindValue(':email', $email);
        $stmtAtualizaPessoa->bindValue(':possui_acesso', $possuiAcessoInserir);

        if(!empty($senha)) {
            $stmtAtualizaPessoa->bindValue(':senha', $senha);
        }

        $stmtAtualizaPessoa->execute();

        $sqlDeletaTelefone = 'DELETE FROM telefone WHERE codigo_pessoa = :codigo_pessoa';
        $stmtDeletaTelefone = $conexao->prepare($sqlDeletaTelefone);
        $stmtDeletaTelefone->bindValue(':codigo_pessoa', $_POST['codigo']);
        $stmtDeletaTelefone->execute();

        $telefones = $_POST['telefone'];

        if(count($telefones) > 0) {
            foreach($telefones as $telefone) {
                $sqlInsereTelefone = 'INSERT INTO telefone (codigo_pessoa, numero) VALUES (:codigo_pessoa, :numero)';
                $stmtInsereTelefone = $conexao->prepare($sqlInsereTelefone);
                $stmtInsereTelefone->bindValue(':codigo_pessoa', $_POST['codigo']);
                $stmtInsereTelefone->bindValue(':numero', $telefone);
                $stmtInsereTelefone->execute();
            }
        }

        $_SESSION['MENSAGEM'] = Constante::MENSAGEM_REGISTRO_ALTERADO;
        executaJs('location.href="'.Constante::LINK.'/pessoa"');
        exit;
    }
}

$selectedAdm         = null;
$selectedVeterinario = null;
$selectedCliente     = null;
$codigoPessoa        = null;
$checkedPossuiAcesso = 'checked="checked"';
$tipo                = null;
$possuiAcesso        = null;
$nome                = null;
$cpf                 = null;
$email               = null;
$cep                 = null;
$logradouro          = null;
$numero              = null;
$complemento         = null;
$bairro              = null;
$cidade              = null;
$estado              = null;
$numeroTelefone      = [];

if($acaoPagina == Constante::ACAO_ALTERAR) {
    if(isset($dados)) {
        if($dados['tipo'] == 1) {
            $selectedAdm = "selected";
        }
        else if($dados['tipo'] == 2) {
            $selectedVeterinario = "selected";
        }
        else if($dados['tipo'] == 3) {
            $selectedCliente = "selected";
        }

        if($dados['possui_acesso'] == 1) {
            $checkedPossuiAcesso = "checked=checked";
        }
        else {
            $checkedPossuiAcesso = null;
        }

        $codigoPessoa = $dados['codigo'];
        $tipo = $dados['tipo'];
        $possuiAcesso = $dados['possui_acesso'];
        $nome = $dados['nome'];
        $cpf = $dados['cpf'];
        $email = $dados['email'];
        $cep = $dados['cep'];
        $logradouro = $dados['logradouro'];
        $numero = $dados['numero'];
        $complemento = $dados['complemento'];
        $bairro = $dados['bairro'];
        $cidade = $dados['cidade'];
        $estado = $dados['estado'];
        $numeroTelefone = $dados['telefone'];
    }
    else {
        executaJs('location.href="'.Constante::LINK.'/pessoa"');
        exit;
    }
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-header">
                <h1><i class="fa fa-user"></i> <?=($acaoPagina=="alterar")?"Alterar":"Incluir";?> <small>Pessoas</small></h1>
            </div>
            <form role="form" action="<?=Constante::LINK.'/pessoa/'.$acaoPagina?>" method="post">
                <input type="hidden" value="<?=$codigoPessoa?>" name="codigo">
                <?php
    if(isUsuarioAdministrador()) {
                ?>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select class="form-control" name="tipo">
                                <option value="3" <?=$selectedCliente?>>Cliente</option>
                                <option value="2" <?=$selectedVeterinario?>>Veterinário</option>
                                <option value="1" <?=$selectedAdm?>>Administrador</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="possui-acesso">Acesso ao sistema?</label>
                            <input type="checkbox" name="possui-acesso" id="possui-acesso" <?=$checkedPossuiAcesso?>>
                        </div>
                    </div>
                </div>
                <?php
    }
                  else {
                ?>
                <input type="hidden" value="<?=$tipo?>" name="tipo"/>
                <input type="hidden" value="<?=$possuiAcesso?>" name="possui-acesso"/>
                <?php
                  }
                ?>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" placeholder="Nome" value="<?=$nome?>">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>CPF</label>
                            <input type="text" name="cpf" class="form-control cpf-validate" placeholder="CPF" value="<?=$cpf?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="text" name="email" class="form-control" placeholder="E-mail" value="<?=$email?>">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>CEP</label>
                            <input type="text" name="cep" class="form-control" placeholder="CEP" value="<?=$cep?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Logradouro</label>
                            <input type="text" name="logradouro" class="form-control" placeholder="Logradouro" value="<?=$logradouro?>">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Número</label>
                            <input type="text" name="numero" class="form-control" placeholder="Número" value="<?=$numero?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Complemento</label>
                            <input type="text" name="complemento" class="form-control" placeholder="Complemento" value="<?=$complemento?>">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" name="bairro" class="form-control" placeholder="Bairro" value="<?=$bairro?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" name="cidade" class="form-control" placeholder="Cidade" value="<?=$cidade?>">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>UF</label>
                            <input type="text" name="estado" class="form-control" placeholder="UF" maxlength="2" value="<?=$estado?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label>Telefones</label>
                        <div class="grid" id="grid-telefone">
                            <?php
    if(count($numeroTelefone) >= 1) {
        foreach($numeroTelefone as $numero) {
                            ?>
                            <div class="container-grid">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <input type="text" name="telefone[]" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" class="form-control" placeholder="telefone" value="<?=$numero?>"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <a class="btn btn-danger btn-del">-</a>
                                        <a class="btn btn-primary btn-add">+</a>
                                    </div>
                                </div>
                            </div>
                            <?php
        }
    }
                                   else {
                            ?>
                            <div class="container-grid">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <input type="text" name="telefone[]" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" class="form-control" placeholder="Telefone"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <a class="btn btn-danger btn-del">-</a>
                                        <a class="btn btn-primary btn-add">+</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                                   }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr />
                        <input type="submit" name="cadastrar" class="btn btn-primary" value="<?=ucfirst($acaoPagina)?>"/>
                        <a href="<?=Constante::LINK.'/pessoa'?>" class="btn btn-secundar btn-voltar">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
