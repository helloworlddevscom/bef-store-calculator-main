import { airportAutocomplete, airportJqueryAutocomplete } from '../calculators/utility';

import { flightTable } from '../calculators/complexTravel';
import { fields, hiddenValues } from './fieldmap';
import * as tableFlightComponent from '../components/tableFlightComponent';
import * as totalCalc from './totalsFlight';
import { annualFlight } from '../calculators/basic';
import * as radioFlight from '../components/radioFlightComponent';

export function fightRadio(formId) {
  const topConfig = {
    tableClassName: 'select__airport-api',
    radioSelectClassName: 'flight-type__select',
    annualFunction: annualFlight,
    selectFunction: annualFlight,
    annualInput: fields.flight_input.id,
    selectInput: fields.flight_select_input.name,
    annualElementTotal: hiddenValues.annual_flight_total.id,
    selectElementTotal: hiddenValues.select_flight_total.id,
    tableElementTotal: hiddenValues.table_flight_total.id,
    radioTotal: hiddenValues.flight_total.id,
  };
  radioFlight.confirmationRadioListener({
    className: topConfig.radioSelectClassName,
    hiddenAnnual: topConfig.annualElementTotal,
    hiddenSelect: topConfig.selectElementTotal,
    hiddenTable: topConfig.tableElementTotal,
    hiddenTotal: topConfig.radioTotal,
    totalCalc,
    formId
  });
  radioFlight.annualListener({
    formId,
    calculation: topConfig.annualFunction,
    inputID: topConfig.annualInput,
    hiddenID: topConfig.annualElementTotal,
    totalCalc,
  });
  radioFlight.rowListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
  });
  radioFlight.selectListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
  });
}

export function flightTravelTable(formId) {
  const topConfig = {
    tableClassName: 'table__annual-num-of-flights',
    tableFunction: flightTable,
    tableCommuteTotal: hiddenValues.table_flight_total.id,
  };
  tableFlightComponent.flightTableListener({
    tableClassName: topConfig.tableClassName,
    calculation: topConfig.tableFunction,
    totalCalc,
    hiddenTOTAL: topConfig.tableCommuteTotal,
    formId,
  });
}

export function formSubmit(formId) {
  // Grab Form
  const selectedElement = document.getElementById(`gform_submit_${formId}`);

  selectedElement.addEventListener(
    'click',
    () => {
      document.getElementById(`gform_source_page_number_${formId}`).value = 1;
      document.getElementById(`gform_target_page_number_${formId}`).value = 0;
      document.forms[`gform_${formId}`].submit();
    },
    false,
  );
}
