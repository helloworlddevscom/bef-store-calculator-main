export default async function googleApiAjax(departAirport, arrivalAirport, el) {
  let responseObj = {};

  jQuery.ajaxSetup({
    headers: { 'Content-Type': 'application/json' },
  });

  await jQuery.ajax({
    type: 'GET',
    dataType: 'JSON',
    url: jsParameters.ajaxurl,
    data: {
      action: 'get_google_distance',
      airport_a: departAirport,
      airport_b: arrivalAirport,
      nonce: jsParameters.nonce,
    },
    success(response) {
      responseObj = { miles: response, el };
    },
    error(xhr, desc, err) {
      console.log(xhr);
      console.log(`Details: ${desc}\nError:${err}`);
    },
  });
  return responseObj;
}
