import * as business from './business';
import * as flight from './flight';
import * as household from './household';

import { removeReportButton } from './components/functions/removeReportButton';
import { tabReturnOverride } from './components/functions/tabReturnOverride';

import { forms } from './calculators/constants';

console.log('..Webpack Build..latest');

if (window.jQuery) {
  jQuery(document).on('gform_post_render', (event, formId, currentPage) => {
    if (formId === forms.businessFormId) {
      console.log('building Business Form');

      tabReturnOverride(window.jQuery);
      removeReportButton(forms.businessFormId);

      business.naturalGasRadio(forms.businessFormId);
      business.fuelOilRadio(forms.businessFormId);
      business.electricityRadio(forms.businessFormId);
      business.waterRadio();

      business.propaneListener();
      business.dieselListener();
      business.offsiteServerListener();

      business.ownedVehicleTable();
      business.commuteTable();

      business.fightRadio(forms.businessFormId);
      business.flightTravelTable(forms.businessFormId);

      business.annualLodgingListener(forms.businessFormId);
      business.annualRentalListener(forms.businessFormId);

      business.formSubmit(forms.businessFormId);
      business.getGridFromZip(forms.businessFormId);
    }
  });

  jQuery(document).on('gform_post_render', (event, formId, currentPage) => {
    if (formId === forms.flightFormId) {
      console.log('building Flight Form');
      tabReturnOverride(window.jQuery);
      flight.flightTravelTable(forms.flightFormId);
      flight.fightRadio(forms.flightFormId);
      flight.formSubmit(forms.flightFormId);
    }
  });

  jQuery(document).on('gform_post_render', (event, formId, currentPage) => {
    if (formId === forms.householdFormId) {
      console.log('building Household Form');

      tabReturnOverride(window.jQuery);
      removeReportButton(forms.householdFormId);

      household.naturalGasRadio(forms.householdFormId);
      household.fuelOilRadio(forms.householdFormId);
      household.electricityRadio(forms.householdFormId);
      household.propaneRadio(forms.householdFormId);
      household.waterRadio();

      household.personalVehicleTable();

      household.fightRadio(forms.householdFormId);
      household.flightTravelTable(forms.householdFormId);

      household.annualLodgingListener(forms.householdFormId);
      household.annualRentalListener(forms.householdFormId);

      household.personalDietTable(forms.householdFormId);

      household.getGridFromZip(forms.householdFormId);

      household.formSubmit(forms.householdFormId);
    }
  });
}
