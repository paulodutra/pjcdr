/** CONTROLE DAS COMBOS*/

/** COMBO DE ESTADO - PAGINA DE CADASTRO DA ESCOLA*/
$(function () {
    $('.j_loadstate').change(function () {
        var uf = $('.j_loadstate');
        var city = $('.j_loadcity');
        var patch = ($('#j_ajaxident').length ? $('#j_ajaxident').attr('class') + '/city.php' : '../_cdn/ajax/city.php');


        city.attr('disabled', 'true');
        uf.attr('disabled', 'true');

        city.html('<option value=""> Carregando cidades... </option>');

        $.post(patch, {estado: $(this).val()}, function (cityes) {
            city.html(cityes).removeAttr('disabled');
            uf.removeAttr('disabled');
        });
    });
});


/** COMBO DE CONCURSO - PAGINA DE INSCRIÇÃO DA ESCOLA NO CONCURSO*/
$(function () {
    $('.j_loadconcurso').change(function () {
        var concurso = $('.j_loadconcurso');
        var category = $('.j_loadcategory');
        var patch = ($('#j_ajaxident1').length ? $('#j_ajaxident1').attr('class') + '/category.php' : '../_cdn/ajax/category.php');

        category.attr('disabled', 'true');
        concurso.attr('disabled', 'true');

        category.html('<option value=""> Carregando Categorias... </option>');

        $.post(patch, {concurso: $(this).val()}, function (concursos) {
            category.html(concursos).removeAttr('disabled');
            concurso.removeAttr('disabled');
        });
    });
});


/** COMBO DE PROFESSORES - PAGINA DE INSCRIÇÃO DA ESCOLA NO CONCURSO*/
$(function () {
    $('.j_loadschool').change(function () {
        var school = $('.j_loadschool');
        var teacher = $('.j_loadteachers');
        var patch = ($('#j_ajaxident2').length ? $('#j_ajaxident2').attr('class') + '/teachers.php' : '../_cdn/ajax/teachers.php');

        teacher.attr('disabled', 'true');
        school.attr('disabled', 'true');

        teacher.html('<option value=""> Carregando Professores... </option>');

        $.post(patch, {escola: $(this).val()}, function (escolas) {
            teacher.html(escolas).removeAttr('disabled');
            school.removeAttr('disabled');
        });
    });
});

/** COMBO DE ESTUDANTES - PAGINA DE INSCRIÇÃO DA ESCOLA NO CONCURSO*/

$(function () {
    $('.j_loadschool').change(function () {
        var school = $('.j_loadschool');
        var student = $('.j_loadstudent');
        var patch = ($('#j_ajaxident3').length ? $('#j_ajaxident3').attr('class') + '/students.php' : '../_cdn/ajax/students.php');

        student.attr('disabled', 'true');
        school.attr('disabled', 'true');

        student.html('<option value=""> Carregando Estudantes... </option>');

        $.post(patch, {escola: $(this).val()}, function (estudantes) {
            student.html(estudantes).removeAttr('disabled');
            school.removeAttr('disabled');
        });
    });
});

/** COMBO DE SERIES - PAGINA DE INSCRIÇÃO DA ESCOLA NO CONCURSO*/
$(function () {
    $('.j_loadcategory').change(function () {
        var category = $('.j_loadcategory');
        var series = $('.j_loadseries');
        var patch = ($('#j_ajaxident4').length ? $('#j_ajaxident4').attr('class') + '/series.php' : '../_cdn/ajax/series.php');

        series.attr('disabled', 'true');
        category.attr('disabled', 'true');

        series.html('<option value=""> Carregando Serie(s)... </option>');

        $.post(patch, {categoria: $(this).val()}, function (categorias) {
            series.html(categorias).removeAttr('disabled');
            category.removeAttr('disabled');
        });
    });
});


$(function () {
    $('.j_loadschoolpage').change(function () {
        var page = $('.j_loadschoolpage');
        var list = $('.j_loadschoollist');
        var patch = ($('#j_ajaxident5').length ? $('#j_ajaxident5').attr('class') + '/school.php' : '../_cdn/ajax/school.php');

        page.attr('disabled', 'true');
        list.attr('disabled', 'true');

        list.html('<option value=""> Carregando Escolas(s)... </option>');

        $.post(patch, {lista: $(this).val()}, function (listas) {
            list.html(listas).removeAttr('disabled');
            page.removeAttr('disabled');
        });
    });
});



