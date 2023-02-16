export default async function egridAjax(zipCode) {
  let eGrid = null;

  jQuery.ajaxSetup({
    headers: { 'Content-Type': 'application/json' },
  });

  await jQuery.ajax({
    type: 'GET',
    dataType: 'JSON',
    url: jsParameters.ajaxurl,
    data: {
      action: 'my_egrid',
      zipcode: zipCode,
      nonce: jsParameters.nonce,
    },
    success(response) {
      eGrid = response.egrid;
      console.log('success!!', response.egrid, ' for zipcode ', zipCode);
    },
    error(xhr, desc, err) {
      console.log(xhr);
      console.log(`Details: ${desc}\nError:${err}`);
    },
  });
  return eGrid;
}
