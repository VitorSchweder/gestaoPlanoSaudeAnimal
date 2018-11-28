<?php
require_once('../include/Constante.php');
require_once(Constante::DIRETORIO_ROOT.'/paginas/PaginaSegura.php');
require_once(Constante::DIRETORIO_ROOT.'/vendor/dompdf/dompdf/dompdf_config.inc.php');

if(isset($_POST['html'])) {
    $html = $_POST['html'];

    /* Cria a instância */
    $dompdf = new DOMPDF();

    /* Carrega seu HTML */
    $dompdf->load_html($html);

    /* Renderiza */
    $dompdf->render();

    $pdfBase64 = base64_encode($dompdf->Output());
    echo 'data:application/pdf;base64,' . $pdfBase64;
}
