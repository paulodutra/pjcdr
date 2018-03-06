$(document).ready(function() {
var valueSelected;
 $("#phone_school").change(function(){
 	var patch = ($('#j_ajaxident').length ? $('#j_ajaxident').attr('class') + '/city.php' : '../_cdn/ajax/city.php');
     //valueSelected=$("#phone_school option:selected").text();
      // obtendo o valor do atributo value da tag option
      valueSelected=$("#phone_school option:selected").val();
      // exibindo uma janela com o valor selecionado
      //alert (valueSelected);

       $.post(patch, {estado: $(this).val()}, function(cityes) ;
   });
});