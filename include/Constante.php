<?php
/*
 * @author Vitor Schweder
 * @since 10/07/2015
 * Classe Responsável pelas constantes do site, mantendo um padrão de chamada de caminhos absolutos,
   links do site ou chamadas de API
 */
class Constante {
    /*
     * Diretórios com o caminho absoluto para requisição de arquivos internamente do servidor
     */
    const DIRETORIO_ROOT = '/home/planbel/public_html/_sistema';

    /*
     * Links dos endereços das páginas para apontamento de endereços em geral
     */
    const LINK = 'http://sistema.planbel.com.br';//'http://www.planbel.com.br';

    const ACAO_INCLUIR = 'incluir';
    const ACAO_ALTERAR = 'alterar';
    const ACAO_EXCLUIR = 'excluir';
    const ACAO_VISUALIZAR = 'visualizar';

    const TIPO_USUARIO_ADMINISTRADOR = 1;
    const TIPO_USUARIO_VETERINARIO = 2;
    const TIPO_USUARIO_CLIENTE = 3;

    const MENSAGEM_REGISTRO_INCLUIDO = 'Registro incluído com sucesso.';
    const MENSAGEM_REGISTRO_ALTERADO = 'Registro alterado com sucesso.';
    const MENSAGEM_REGISTRO_EXCLUIDO = 'Registro excluído com sucesso.';
}
