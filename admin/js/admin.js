$(function () {
    //SHADOWBOX
    //Shadowbox.init();


    /**************************************/
    /**********MASCARAS********************/
    /**************************************/


    $("#cnpj").mask("99.999.999/9999-99");
    $("#cep").mask("99999-999");
    $("#phone").mask("(99) 9999-9999?9");
    $("#nascimento").mask("99/99/9999");
    $("#cpf").mask("999.999.999-99");
    $("#agenciabb").mask("9999-*");
    $("#contabb").mask("99999999-*");
    $("#agenciacef").mask("9999");
    $("#contacef").mask("99999999999-9");



    //Jquery datetime picke
    jQuery('#start').datetimepicker({
        lang: 'pt',
        timepicker: true,
        format: 'd/m/Y H:i:s'
    });


    jQuery('#end').datetimepicker({
        lang: 'pt',
        timepicker: true,
        format: 'd/m/Y H:i:s'
    });

    jQuery('#nascimento').datetimepicker({
        lang: 'pt',
        timepicker: false,
        format: 'd/m/Y'
    });

    jQuery('#datepost').datetimepicker({
        lang: 'pt',
        timepicker: true,
        format: 'd/m/Y'
    });


    /**************************************/
    /**********FORMULARIO******************/
    /**************************************/

    /**FORMUL�RIO DE CADASTRO DE CONTA BANC�RIA*/
    $(document).ready(function () {

        $("#cef").hide();
        $("#bb").hide();

        $("#data_bank_bank").change(function () {
            /*BANCO DO BRASIL*/
            if ($(this).val() === "1") {

                $("#agenciacef").val('');
                $("#contacef").val('');

                $("#bb").show();
                $("#cef").hide();

                $("#agenciabb").removeAttr("disabled");
                $("#contabb").removeAttr("disabled");

                $("#agenciacef").attr("disabled", "disabled");
                $("#contacef").attr("disabled", "disabled");


            }

            /*CAIXA ECONOMICA FEDERAL*/
            if ($(this).val() === "2") {


                $("#agenciabb").val('');
                $("#contabb").val('');

                $("#cef").show();
                $("#bb").hide();

                $("#agenciacef").removeAttr("disabled");
                $("#contacef").removeAttr("disabled");

                $("#agenciabb").attr("disabled", "disabled");
                $("#contabb").attr("disabled", "disabled");



            }

        });

    });

    /**FORMUL�RIO DE PESQUISA DE ESCOLA*/
    $(document).ready(function () {

        $("#cnpjvalue").hide();
        $("#inepvalue").hide();
        $("#nomevalue").hide();

        $("#parametro").change(function () {
            /*INEP*/
            if ($(this).val() === "1") {

                $("#cnpj").val('');
                $("#inep").val('');
                $("#nome").val('');


                $("#cnpjvalue").hide();
                $("#nomevalue").hide();
                $("#inepvalue").show();

                $("#inep").removeAttr("disabled");


                $("#cnpj").attr("disabled", "disabled");
                $("#nome").attr("disabled", "disabled");


            }
            /*CNPJ*/
            if ($(this).val() === "2") {

                $("#cnpj").val('');
                $("#inep").val('');
                $("#nome").val('');

                $("#inepvalue").hide();
                $("#nomevalue").hide();
                $("#cnpjvalue").show();



                $("#cnpj").removeAttr("disabled");


                $("#inep").attr("disabled", "disabled");
                $("#nome").attr("disabled", "disabled");

            }

            /*NOME OU PARTE DO NOME*/
            if ($(this).val() === "3") {

                $("#cnpj").val('');
                $("#inep").val('');
                $("#nome").val('');

                $("#inepvalue").hide();
                $("#cnpjvalue").hide();
                $("#nomevalue").show();



                $("#nome").removeAttr("disabled");


                $("#inep").attr("disabled", "disabled");
                $("#cnpj").attr("disabled", "disabled");

            }
        });


    });


    /**************************************/
    /*************DATA TABLE***************/
    /**************************************/

    //Data table
    $(document).ready(function () {
        $('#school').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [
                {extend: 'copy', text: 'Copiar'},
                {extend: 'excel', text: 'Exportar para excel'},
                {extend: 'pdf', text: 'Exportar para pdf'},
                {extend: 'print', text: 'Imprimir'},
            ],
            language: {
                search: "Pesquisar",
                processing: "Aguarde a requisÃ£o",
                info: "Mostrando de _START_ ate _END_ de _TOTAL_ resultados",
                infoEmpty: "Exibindo 0 de 0 itens",
                infoFiltered: "(Mostrar _MAX_ registros por pÃ¡gina)",
                infoPostFix: "",
                loadingRecords: "Carregando registros, favor aguarde !",
                zeroRecords: "Nenhum resultado encontrado",
                emptyTable: "Não há dados disponíveis na tabela !",
                lengthMenu: "Mostrar _MENU_ registros",
                paginate: {
                    first: "Primeiro",
                    previous: "Anterior",
                    next: "Proximo",
                    last: "Ãºltimo"

                },
                aria: {
                    sortAscending: "habilitar para classificar a coluna em ordem crescente",
                    sortDescending: "habilitar para classificar a coluna em ordem decrescente"
                }

            }


        });
    });

    $(document).ready(function () {
        $('#school-page').DataTable({
            paging: false,
            dom: 'B<"clear">lfrtip',
            buttons: [
                {extend: 'copy', text: 'Copiar'},
                {extend: 'excel', text: 'Exportar para excel'},
                {extend: 'pdf', text: 'Exportar para pdf'},
                {extend: 'print', text: 'Imprimir'},
            ],
            language: {
                search: "Pesquisar",
                processing: "Aguarde a requisição",
                info: "Mostrando de _START_ ate _END_ de _TOTAL_ resultados",
                infoEmpty: "Exibindo 0 de 0 itens",
                infoFiltered: "(Mostrar _MAX_ registros por página)",
                infoPostFix: "",
                loadingRecords: "Carregando registros, favor aguarde !",
                zeroRecords: "Nenhum resultado encontrado",
                emptyTable: "Não há dados disponíveis na tabela !",
                lengthMenu: "Mostrar _MENU_ registros",
                paginate: {
                    first: "Primeiro",
                    previous: "Anterior",
                    next: "Proximo",
                    last: "Último"

                },
                aria: {
                    sortAscending: "habilitar para classificar a coluna em ordem crescente",
                    sortDescending: "habilitar para classificar a coluna em ordem decrescente"
                }

            }


        });
    });


    /**************************************/
    /***************TINYMCE****************/
    /**************************************/


    //TinyMCE
    //EXTENSÃ‚O DE YOUTUBE EM \tiny_mce\plugins\media\js MEDIA.js
    tinyMCE.init({
        // General options
        mode: "specific_textareas",
        editor_selector: "js_editor",
        language: "pt",
        theme: "advanced",
        elements: 'abshosturls',
        relative_urls: false,
        remove_script_host: false,
        skin: "o2k7",
        skin_variant: "silver",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
        theme_advanced_blockformats: "p,h2,h3,h4,pre,address",
        // Theme options
        theme_advanced_buttons1: "fullscreen,|,undo,redo,|,code,|,pastetext,|,removeformat,|,formatselect,bold,italic,underline,|,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,anchor,|,image,|,media,|,blockquote,|,hr,|,outdent,indent,|,charmap",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        theme_advanced_buttons4: "",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "center",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: false,
        // Example content CSS (should be your site CSS)
        content_css: "css/tiny.css",
        // Drop lists for link/image/media/template dialogs
        template_external_list_url: "lists/template_list.js",
        external_link_list_url: "lists/link_list.js",
        external_image_list_url: "lists/image_list.js",
        media_external_list_url: "lists/media_list.js",
        file_browser_callback: "tinyBrowser",
        // Style formats
        style_formats: [
            {title: 'Bold text', inline: 'b'},
            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            {title: 'Example 1', inline: 'span', classes: 'example1'},
            {title: 'Example 2', inline: 'span', classes: 'example2'},
            {title: 'Table styles'},
            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ],
        // Replace values for the template plugin
        template_replace_values: {
            username: "TECNOLOGIA",
            staffid: "991234"
        }
    });
});