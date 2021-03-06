jQuery.noConflict();
jQuery('#credito_portador_telefone').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});
jQuery('#credito_portador_cpf').mask("000.000.000-00", {placeholder: "___.___.___-__"});
jQuery('#credito_portador_nascimento').mask("99/99/9999", {placeholder: "__/__/____"});

jQuery("#credito_expiracao_ano").change(function() {
 var d = new Date();
    var m = d.getMonth()+1;
    var str= d.getFullYear()+'';
    y_atual= str.match(/\d{2}$/);
    var select_y = jQuery(this).val();
    var select_m = jQuery("#credito_expiracao_mes").val();

    if(select_y == y_atual){
        if(select_m < m ){
            jQuery(".alerta_data").show();
            jQuery(".alerta_data").html('<li class="error-msg"><ul><li><span>Seu cartão está expirado ou com data incorreta. Sua transação não será aceita, por favor corriga o dado.</span></li></ul></li>');
            jQuery("#checkout-onepage-buttom").attr("disabled","disabled");
        }
        else{
            jQuery(".alerta_data").hide();
            jQuery("#checkout-onepage-buttom").attr("disabled");
        }
    }
    else{
            jQuery(".alerta_data").hide();
            jQuery("#checkout-onepage-buttom").removeAttr("disabled");
        }
});
jQuery("#credito_expiracao_mes").change(function() {
    var d = new Date();
    var m = d.getMonth()+1;
    var str= d.getFullYear()+'';
    y_atual= str.match(/\d{2}$/);
    var select_m = jQuery(this).val();
    var select_y = jQuery("#credito_expiracao_ano").val();
    if(select_y == y_atual && select_y != ""){
        if(select_m < m ){
            jQuery(".alerta_data").show();
            jQuery(".alerta_data").html('<li class="error-msg"><ul><li><span>Seu cartão está expirado ou com data incorreta. Sua transação não será aceita, por favor corriga o dado.</span></li></ul></li>');
        }
        else{
            jQuery(".alerta_data").hide();
        }
    }
    else{
            jQuery(".alerta_data").hide();
        }
});
jQuery( "#credito_numero_moip" ).focusout(function() {
      valor = jQuery( "#credito_numero_moip" ).val();
      valide = moip.creditCard.isValid(valor);
      if(valide){
        result = moip.creditCard.cardType(valor);
        jQuery("#credito_instituicao_moip").addClass('validation-passed');
        jQuery("#credito_instituicao_moip").val(result.brand);
        jQuery("#Moip_"+result.brand).css({
          opacity: '1'
        });
        jQuery( "#cartao_valido" ).val('1');
      } else{
        jQuery("#credito_instituicao_moip").removeClass('validation-passed');
        jQuery("#credito_instituicao_moip").addClass('validation-failed');
        jQuery( "#cartao_valido" ).val();
      }
  }).blur(function() {
      valor = jQuery( "#credito_numero_moip" ).val();
      valide = moip.creditCard.isValid(valor);
      if(valide){
        result = moip.creditCard.cardType(valor);
        jQuery("#credito_instituicao_moip").addClass('validation-passed');
        jQuery("#credito_instituicao_moip").val(result.brand);
        jQuery("#Moip_"+result.brand).show();
        jQuery( "#cartao_valido" ).val('1');
      } else {
        jQuery("#credito_instituicao_moip").removeClass('validation-passed');
        jQuery("#credito_instituicao_moip").addClass('validation-failed');
        jQuery( "#cartao_valido" ).val();
      }

  });


jQuery( "#credito_codigo_seguranca" ).focusout(function() {
    valide = moip.creditCard.isSecurityCodeValid(jQuery( "#credito_numero_moip" ).val(), jQuery( "#credito_codigo_seguranca" ).val());
  if(valide){
    jQuery("#credito_codigo_seguranca").addClass('validation-passed');
    jQuery( "#cvv_valido" ).val('1');
  } else {
    jQuery("#credito_codigo_seguranca").removeClass('validation-passed');
    jQuery("#credito_codigo_seguranca").addClass('validation-failed');
    jQuery( "#cvv_valido" ).val();
  }
  jQuery(".dados-titular").show("slow");
  jQuery("#formcli").show("slow");
  document.getElementById('credito_portador_nome').value = document.getElementById('billing:firstname').value + ' ' + document.getElementById('billing:lastname').value;
  document.getElementById('credito_portador_telefone').value = document.getElementById('billing:telephone').value;
  document.getElementById('credito_portador_cpf').value = document.getElementById('billing:taxvat').value;
  if (document.getElementById('billing:year').value) {
    document.getElementById('credito_portador_nascimento').value = document.getElementById('billing:day').value + '/' + document.getElementById('billing:month').value + '/' + document.getElementById('billing:year').value
  }
  })
  .blur(function() {
      valide = moip.creditCard.isSecurityCodeValid(jQuery( "#credito_numero_moip" ).val(), jQuery( "#credito_codigo_seguranca" ).val());
  if(valide){
    jQuery("#credito_codigo_seguranca").addClass('validation-passed');
    jQuery( "#cvv_valido" ).val('1');
  } else {
    jQuery("#credito_codigo_seguranca").removeClass('validation-passed');
    jQuery("#credito_codigo_seguranca").addClass('validation-failed');
    jQuery( "#cvv_valido" ).val();
  }
  jQuery(".dados-titular").show("slow");
  jQuery("#formcli").show("slow");
  document.getElementById('credito_portador_nome').value = document.getElementById('billing:firstname').value + ' ' + document.getElementById('billing:lastname').value;
  document.getElementById('credito_portador_telefone').value = document.getElementById('billing:telephone').value;
  document.getElementById('credito_portador_cpf').value = document.getElementById('billing:taxvat').value;
  if (document.getElementById('billing:year').value) {
    document.getElementById('credito_portador_nascimento').value = document.getElementById('billing:day').value + '/' + document.getElementById('billing:month').value + '/' + document.getElementById('billing:year').value
  }
  });



jQuery('#onestep_form :input').blur(function() {
    if(jQuery(this).attr('id') != "billing:day" && jQuery(this).attr('id') != "billing:month"){
        Validation.validate(jQuery(this).attr('id'));
    }
});
jQuery('.tooltip-transparente-handler').hover(function() {
    var hover_class = jQuery(this).parent().find('.tooltip-transparente').attr('class').split(' ').pop();
    jQuery('.'+hover_class).addClass('tooltip-transparente-visible');
    setTimeout(function(){jQuery('.'+hover_class).removeClass('tooltip-transparente-visible');}, 5000);
},function() {
    jQuery('.tooltip-transparente').removeClass('tooltip-transparente-visible');
});

function Cofre(cofre_brand, numero_cofre){
    jQuery("#brand_cofre").val(cofre_brand);
    jQuery("#cofre_numero").val(numero_cofre);
}
function Novo(){
    if(jQuery("#new_card").is(":checked")){
      jQuery(".form-list-cofre").hide();
      jQuery(".cartao").show();
      jQuery(".formcli").show();
      jQuery("#use_cofre").val('0');
    }
}
