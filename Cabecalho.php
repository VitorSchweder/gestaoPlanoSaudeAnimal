<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Sistema Gestão de Pet">
        <meta name="author" content="vitor schweder">
        <meta name="robots" content="noindex, nofollow">

        <title>Planbel</title>

        <!-- Bootstrap Core CSS -->
        <link href="<?=Constante::LINK?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="<?=Constante::LINK?>/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="<?=Constante::LINK?>/dist/css/sb-admin-2.css" rel="stylesheet">

        <!-- DataTables CSS -->
        <link href="<?=Constante::LINK?>/vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

        <!-- DataTables Responsive CSS -->
        <link href="<?=Constante::LINK?>/vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="<?=Constante::LINK?>/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    </head>
    <body>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <?php
                if(isset($_SESSION['ID_USUARIO'])) {
            ?>
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?=Constante::LINK.'/home'?>">Planbel - Plano de Saúde Pet</a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <!--<li><a href="#"><i class="fa fa-user fa-fw"></i> Dados do usuário</a>
                            </li>
                            <li><a href="#"><i class="fa fa-gear fa-fw"></i> Configurações</a>
                            </li>
                            -->
                            <li><a href="<?=Constante::LINK.'/logout'?>"><i class="fa fa-sign-out fa-fw"></i> Sair</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                </ul>
                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                        <?php
                            if(isUsuarioAdministrador()) {
                        ?>
                                <li>
                                    <a href="<?=Constante::LINK.'/pessoa'?>">
                                        <i class="fa fa-user"></i> Pessoas
                                    </a>
                                </li>
                                <li>
                                    <a href="<?=Constante::LINK.'/procedimento'?>">
                                        <i class="fa fa-bars"></i> Procedimentos
                                    </a>
                                </li>
                                <li>
                                    <a href="<?=Constante::LINK.'/plano'?>">
                                        <i class="fa fa-clone"></i> Planos
                                    </a>
                                </li>
                                <li>
                                    <a href="<?=Constante::LINK.'/pet'?>">
                                        <i class="fa fa-paw"></i> Pets
                                    </a>
                                </li>
                        <?php
                            }
                            if(!isUsuarioVeterinario()) {
                        ?>
                                <li>
                                    <a href="<?=Constante::LINK.'/contrato'?>">
                                        <i class="fa fa-file-text"></i> Contratos
                                    </a>
                                </li>
                                <li>
                                    <a href="<?=Constante::LINK.'/financeiro'?>">
                                        <i class="fa fa-money"></i> Financeiro
                                    </a>
                                </li>
                            <?php
                            }
                            else {
                            ?>
                                <li>
                                    <a href="<?=Constante::LINK.'/pet'?>">
                                        <i class="fa fa-paw"></i> Pets
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                            <li>
                                <a href="<?=Constante::LINK.'/consulta'?>">
                                    <i class="fa fa-stethoscope"></i> Consultas
                                </a>
                            </li>
                            <li>
                                <a href="<?=Constante::LINK.'/alterarSenha'?>">
                                    <i class="fa fa-key"></i> Alterar minha senha
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>
