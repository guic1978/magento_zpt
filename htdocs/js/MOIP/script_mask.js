jQuery.noConflict();
  jQuery('#billing\\:taxvat').mask("000.000.000-00", {placeholder: "___.___.___-__"});
  jQuery('#shipping\\:taxvat').mask("000.000.000-00", {placeholder: "___.___.___-__"});
  jQuery('#taxvat').mask("000.000.000-00", {placeholder: "___.___.___-__"});
  jQuery('#credito_portador_cpf').mask("000.000.000-00", {placeholder: "___.___.___-__"});
  jQuery('#credito_portador_nascimento').mask("99/99/9999", {placeholder: "__/__/____"});
  jQuery('#cnpj').mask("00.000.000-0000-00", {placeholder: "__.___.___-____-__"});
  jQuery('#billing\\:postcode').mask("00000-000", {placeholder: "_____-___"});
  jQuery('#shipping\\:postcode').mask("00000-000", {placeholder: "_____-___"});
  jQuery('#postcode').mask("00000-000", {placeholder: "_____-___"});
  jQuery('#zip').mask("00000-000", {placeholder: "_____-___"});
  jQuery('#billing\\:fax').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});
  jQuery('#billing\\:telephone').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});
  jQuery('#shipping\\:telephone').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});
  jQuery('#shipping\\:fax').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});
  jQuery('#credito_portador_telefone').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});
  jQuery('#telephone').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});
  jQuery('#fax').mask("(00)0000-0000Z", {placeholder: "(__)____-_____",translation: {'Z': {pattern: /[0-9]/, optional: true}}});

