$(function () {


//MASCARAS
    $(".cnpj").mask("99.999.999/9999-99");


//FORMULÁRIO DE LOGIN DAS ESCOLAS
    $("#formRemenber").hide();
    $("#formFirstAccess").hide();
    $("#formFirstAccess").hide();

    $("#Remenber").click(function () {
        $("#formRemenber").show();
        $("#formLogin").hide();
        $("#formFirstAccess").hide();

    });

    $("#Login").click(function () {
        $("#formLogin").show();
        $("#formRemenber").hide();
        $("#formFirstAccess").hide();

    });

    $("#Login2").click(function () {
        $("#formLogin").show();
        $("#formRemenber").hide();
        $("#formFirstAccess").hide();

    });

    $("#loginForm").submit(function () {
        $("#formLogin").show();
        $("#formRemenber").hide();
        $("#formFirstAccess").hide();

    });

    $("#firstAccess").click(function () {
        $("#formFirstAccess").show();
        $("#formLogin").hide();
        $("#formRemenber").hide();
    });






});