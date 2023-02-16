export default async function airportAutocomplete(departCodeListener) {
  departCodeListener = jQuery(departCodeListener);

  await departCodeListener.autocomplete({
    source(el, response) {
      el = departCodeListener.val();
      jQuery.ajax({
        type: 'GET',
        dataType: 'JSON',
        url: jsParameters.ajaxurl,
        data: {
          action: 'get_all_airports',
          airport: el,
          nonce: jsParameters.nonce,
        },
        success(data) {
          if (response) {
            console.log('success!!');
            return response(data);
          }
          departCodeListener.val('!!-- ERROR --!!');
          console.log('We couldn\'t find that airport!');
        },
      });
    },
    delay: 200,
    minLength: 4,
  });
}
