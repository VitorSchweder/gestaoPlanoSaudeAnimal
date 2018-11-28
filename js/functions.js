var rel = '';

$(function() {
    $('input[name="cpf"]').mask('999.999.999-99');
    $('input[name="cep"]').mask('99999-999');
    $('input[name="vacina"]').mask('99/99/9999');
    $('input[name="telefone[]"]').mask('(00) 0000-00009');
    $('input[name="valor"]').mask('#.##0,00', {reverse: true});
    $('input[name="valor-fixo-desconto[]"]').mask('#.##0,00', {reverse: true});
    $('input[name="porcentagem-procedimento"]').mask('#.##0,00', {reverse: true});

    //    var ativa = $('td.ativa').html();
    //    $('td.etiqueta').click(function () {
    //
    //        var htmlContainer = btoa($(this).parent().parent().parent().html());
    //
    //        //window.open('http://sistema.planbel.com.br/pdfEtiqueta.php?pdf='+htmlContainer, '_blank');
    //
    //        $('td.ativa').html('');
    //        $(this).html(ativa);
    //        $(this).addClass("ativa");
    //
    ////        var htmlContainer = $(this).parent().parent().parent().html();
    //
    ////        window.open('http://sistema.planbel.com.br/pdfEtiqueta.php?p='+btoa(htmlContainer), '_blank');
    //
    ///*        var parametro = {"html" : htmlContainer};
    //
    //        $.ajax({
    //            type: "POST",
    //            url: '../ajax/ImprimeEtiqueta.php',
    //            data: parametro,
    //            dataType : 'application/json',
    //            beforeSend: function() {
    //
    //            },
    //            complete: function(retorno) {
    //                window.open('http://sistema.planbel.com.br/pdfEtiqueta.php?p='+retorno.responseText, '_blank');
    //            }
    //        });*/
    //    });

    $('input[name="cpf"]').blur(function(){
        if($(this).val()) {
            var valorCampo = $(this).val().replace('-', '').split('.');
            valorCampo = valorCampo[0]+valorCampo[1]+valorCampo[2];

            if(!validaCpf(valorCampo)){
                alert('CPF inválido!');
                $('input[name="cpf"]').val('');
                $('input[name="cpf"]').focus();
            }
        }
    });

    $('form').submit(function(e){
        var possuiErro = false;
        var mensagem = '';

        if($(this).attr('id') != 'pet' && $(this).attr('id') != 'consulta') {
            var inputs = $('form :input[type="text"]');

            $(inputs).each(function(){
                if(!$(this).val() && $(this).attr('name') != 'complemento') {
                    possuiErro = true;
                    return false;
                }
            });

            if(possuiErro) {
                alert('Todos os campos são obrigatórios');
                e.preventDefault();
            }
        }
        else if($(this).attr('id') == 'pet'){
            var nomePessoa = $('form#pet input#nome-pessoa').val();
            var nomePet = $('form#pet input[name="nome"]').val();
            var raca = $('form#pet input[name="raca"]').val();

            if(!nomePessoa) {
                mensagem += 'Informe um Proprietário\n';
            }

            if(!nomePet) {
                mensagem += 'Informe um Pet\n';
            }

            if(!raca) {
                mensagem += 'Informe uma Raça\n';
            }

            if(mensagem) {
                alert(mensagem);
                e.preventDefault();
            }
        }
        else if($(this).attr('id') == 'consulta'){
            var nomePessoa = $('form#consulta input#nome-pessoa').val();
            if(!nomePessoa) {
                mensagem += 'Informe um Proprietário\n';
            }

            if(mensagem) {
                alert(mensagem);
                e.preventDefault();
            }
        }
    });

    $(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent();

    while (true) {
        if (element.is('li')) {
            element = element.parent().addClass('in').parent();
        } else {
            break;
        }
    }

    $('input[name="cep"]').blur(function(){
        if($(this).val()) {
            var cep = $(this).val();
            var parametro = {"cep":cep};

            $.ajax({
                type: "POST",
                url: 'http://sistema.planbel.com.br/ajax/RetornaDadosCidade.php',
                data: parametro,
                dataType : 'json',
                beforeSend: function() {
                    $('div#wrapper').append('<div id="carregando"><div id="fundo"></div><img src="http://sistema.planbel.com.br/assets/img/carregando.gif" height="100px"/></div>');
                },
                success: function(retorno) {
                    $('div#wrapper div#carregando').remove();

                    if(retorno.logradouro) {
                        $('input[name="logradouro"]').val(retorno.logradouro);
                        $('input[name="bairro"]').val(retorno.bairro);
                        $('input[name="cidade"]').val(retorno.localidade);
                        $('input[name="estado"]').val(retorno.uf);
                    }
                }
            });
        }
    });

    $(document).on('click','div#grid-telefone a.btn-add', function(){
        $('div#grid-telefone div.container-grid').last().clone().appendTo('div#grid-telefone');
        $('div#grid-telefone div.container-grid').last().find('input').val('');
        $('div#grid-telefone div.container-grid').last().find('input[name="telefone[]"]').mask('(00) 0000-00009');
    });

    $(document).on('click','div#grid-telefone a.btn-del', function(){
        if($('div#grid-telefone div.container-grid').length > 1) {
            $('div#grid-telefone div.container-grid').last().remove();
        }
    });

    $(document).on('click','div#grid-procedimento a.btn-add', function(){
        $('div#grid-procedimento div.container-grid').last().clone().appendTo('div#grid-procedimento');
        $('div#grid-procedimento div.container-grid').last().find('input').val('');
    });

    $(document).on('click','div#grid-procedimento a.btn-del', function(){
        if($('div#grid-procedimento div.container-grid').length > 1) {
            $(this).parent().parent().parent().remove();
        }
    });

    $(document).on('click','div#grid-pet a.btn-add', function(){
        $('div#grid-pet div.container-grid').last().clone().appendTo('div#grid-pet');
        $('div#grid-pet div.container-grid').last().find('input').val('');
    });

    $(document).on('click','div#grid-pet a.btn-del', function(){
        if($('div#grid-pet div.container-grid').length > 1) {
            $(this).parent().parent().parent().remove();
        }
    });

    $('input[name="porcentagem-procedimento"]').blur(function(){
        var valor = $(this).val();
        var tipoDesconto = '';

        if(valor) {
            $('div#grid-procedimento div.container-grid').each(function(){
                var campoProcedimento = $(this).find('input[rel="valor"]');
                var campoProcedimentoValorFixoDesconto = $(this).find('input[rel="valor-fixo-desconto"]');

                tipoDesconto = $(this).find('input[rel="tipo-desconto"]').val();

                //Porcentagem
                if(tipoDesconto == 2) {
                    auxValorProcedimento = (valor * campoProcedimento.val()) / 100;
                    valorProcedimento = campoProcedimento.val() - auxValorProcedimento;

                    if(valorProcedimento < 0) {
                        valorProcedimento = 0;
                    }

                    campoProcedimentoValorFixoDesconto.val(number_format(valorProcedimento, 2, ',', '.'));
                }
            });
        }
    });

    $('.tabela-consulta').DataTable({
        responsive: true,
        language: {
            paginate: {
                first:    'Primeiro',
                previous: 'Anterior',
                next:     'Próximo',
                last:     'Último'
            }
        }
    });

    if($('.alert').length) {
        setTimeout(function(){
            $('.alert').remove();
        },4000);
    }

    rel = $('input#nome-pessoa').attr('rel');

    if(!rel) {
        rel = 'padrao';
    }

    $('input#nome-pessoa').autocomplete({
        minLength : 1,
        autoFocus: true,
        delay : 100,
        appendTo: 'div.resultado-busca-pessoa',
        source: '../ajax/RetornaDadosPessoa.php?rel='+rel,
        select: function (event, ui) {
            var id = ui.item.id;
            var value = ui.item.value;

            $('input#nome-pessoa').val(value);
            $('input[name="codigo-pessoa"]').val(id);

            var identificadorResultado = $('div.resultado-busca-pessoa').attr('id');

            if(identificadorResultado == 'consulta') {
                retornaDadosPet(id);
            }
            else if(identificadorResultado == 'contrato') {
                retornaDadosPetContrato(id);
            }

        }
    });

    $(document).on('click','div.resultado-busca-pessoa#contrato ul li.encontrado',function(){
        retornaDadosPetContrato($(this));
    });

    $('select[name="pet"]').change(function(){
        var codigo = $(this).val();

        retornaDadosProcedimentoPesoPet(codigo);
    });

    function retornaDadosProcedimentoPesoPet(codigo) {
        var parametro = {"codigo":codigo};

        $.ajax({
            type: "POST",
            url: '../ajax/RetornaDadosProcedimentoPesoPet.php',
            data: parametro,
            dataType : 'html',
            beforeSend: function() {
                // $('div#wrapper').append('<div id="carregando"><div id="fundo"></div><img src="http://localhost/planbel/assets/img/carregando.gif" height="100px"/></div>');
            },
            success: function(retorno) {
                if(retorno) {
                    $('select[name="procedimento[]"]').html(retorno);
                }
            }
        });
    }

    $(document).on('click', 'a.acao-parcela', function(event){
        event.preventDefault();
        var codigo = $(this).parent().parent().find('td[rel="codigo"]').html();
        var rel = $(this).attr('rel');
        var acao = '';
        var objetoAtual = $(this);

        if(rel == 'baixa-parcela') {
            acao = 'baixa';
        }
        else if(rel == 'estorno-parcela') {
            acao = 'estorno';
        }

        var parametro = {"codigoParcela" : codigo, "acao" : acao};

        $.ajax({
            type: "POST",
            url: '../../ajax/AlteraSituacaoParcela.php',
            data: parametro,
            dataType : 'json',
            beforeSend: function() {
                $(objetoAtual).html('...');
            },
            success: function(retorno) {
                if(!retorno.erro) {
                    location.href = window.location.href ;
                }
            }
        });
    });

    function retornaDadosPetContrato(codigo) {
        var parametro = {"codigoPessoa":codigo};

        $.ajax({
            type: "POST",
            url: '../ajax/RetornaDadosPetContrato.php',
            data: parametro,
            dataType : 'html',
            beforeSend: function() {
                // $('div#wrapper').append('<div id="carregando"><div id="fundo"></div><img src="http://localhost/planbel/assets/img/carregando.gif" height="100px"/></div>');
            },
            success: function(retorno) {
                if(retorno) {
                    $('div#grid-pet').html(retorno);
                }
                else {
                    alert('Não existe pet cadastrado para esse cliente');
                    $('div#grid-pet').html('');
                }
            }
        });
    }

    function retornaDadosPet(codigo) {
        var parametro = {"codigoPessoa":codigo};

        $.ajax({
            type: "POST",
            url: '../ajax/RetornaDadosPet.php',
            data: parametro,
            dataType : 'html',
            beforeSend: function() {
                // $('div#wrapper').append('<div id="carregando"><div id="fundo"></div><img src="http://localhost/planbel/assets/img/carregando.gif" height="100px"/></div>');
            },
            success: function(retorno) {
                if(retorno) {
                    $('select[name="pet"]').html(retorno);

                    if($('select[name="pet"]').find('option#situacao').html() == 0) {
                        alert('Financeiro do cliente em atraso.');
                        $('input#nome-pessoa').val('');
                    }

                    retornaDadosProcedimentoPesoPet($('select[name="pet"]').val());
                }
                else {
                    alert('Não existe pet cadastrado para esse cliente');
                    $('select[name="pet"]').html('<option value="">Informe o cliente</option>');
                }
            }
        });
    }

    function number_format (number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
        var n = !isFinite(+number) ? 0 : +number
        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
        var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
        var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
        var s = ''
        var toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec)
            return '' + (Math.round(n * k) / k)
                .toFixed(prec)
        }
        // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || ''
            s[1] += new Array(prec - s[1].length + 1).join('0')
        }
        return s.join(dec)
    }

    function validaCpf(strCPF) {
        var Soma;
        var Resto;
        Soma = 0;
        if (strCPF == "00000000000") return false;

        for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

        Soma = 0;
        for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
        Resto = (Soma * 10) % 11;

        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;

        return true;
    }
});
