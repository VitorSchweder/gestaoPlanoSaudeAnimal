<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/vendor/dompdf/dompdf/dompdf_config.inc.php');

$auxPdf = null;
$auxPdf = explode('?p=',$_SERVER['REQUEST_URI']);

$pdf = base64_decode($auxPdf[1]);

/* Cria a instância */
$dompdf = new DOMPDF();

/* Carrega seu HTML */
$dompdf->load_html($pdf);

/* Renderiza */
$dompdf->render();

$dompdf->stream(
    "saida.pdf", /* Nome do arquivo de saída */
    array(
        "Attachment" => false /* Para download, altere para true */
    )
);

?>
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

        <!-- jQuery -->
        <script src="<?=Constante::LINK?>/vendor/jquery/jquery.min.js"></script>


        <script src="<?=Constante::LINK?>/vendor/jquery/jquery-ui.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="<?=Constante::LINK?>/vendor/bootstrap/js/bootstrap.min.js"></script>

        <div id="pdf">
          <object width="100%" height="1080" type="application/pdf" data="<?=$pdf?>" id="pdf_content">
          </object>
        </div>
    </head>
</html>
