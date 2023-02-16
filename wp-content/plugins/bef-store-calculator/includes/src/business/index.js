import {
  annualNaturalGas, annualFuelOil, annualPropane, annualDiesel, offsiteServer, annualFlight, annualLodging, annualRentalCar,
} from '../calculators/basic';
import { ownedVehicleFleet, commuting } from '../calculators/complexCommute';
import {
  offsiteREC, waterWRC, electricityREC, electricitySelectREC,
} from '../calculators/rec_wrc';
import { flightTable } from '../calculators/complexTravel';

import {
  naturalGasBuilding, fuelOilBuilding, waterBuilding, electricityBuilding,
} from '../calculators/dropDown';
import { annualElectricity } from '../calculators/complexElectricty';

import { fields, hiddenValues } from './fieldmap';
import { egridAjax } from '../calculators/utility';

import * as totalCalc from './totalsBusiness';
import * as radioComponent from '../components/radioComponent';
import * as radioElectricityComponent from '../components/radioElectricityComponent';
import * as radioWaterComponent from '../components/radioWaterComponent';
import * as radioFlight from '../components/radioFlightComponent';
import * as tableCommuteComponent from '../components/tableCommuteComponent';
import * as tableOwnedVehicleComponent from '../components/tableOwnedVehicleFleet';
import * as tableFlightComponent from '../components/tableFlightComponent';
import { forms } from '../calculators/constants';
import { annualElectricityCalc } from '../components/electricityFunctions/annualElectricityCalc';
import { selectElectricityCalc } from '../components/electricityFunctions/selectElectricityCalc';

export function commuteTable() {
  const topConfig = {
    tableClassName: 'table__commute-vehicle',
    tableFunction: commuting,
    tableCommuteTotal: hiddenValues.commuting_total.id,
  };
  tableCommuteComponent.commutingListener({
    tableClassName: topConfig.tableClassName,
    calculation: topConfig.tableFunction,
    totalCalc,
    hiddenTOTAL: topConfig.tableCommuteTotal,
  });
}

export function ownedVehicleTable() {
  const topConfig = {
    tableClassName: 'table__owned-vehicle',
    tableFunction: ownedVehicleFleet,
    tableCommuteTotal: hiddenValues.ownedVehicle_total.id,
  };
  tableOwnedVehicleComponent.ownedVehicleListener({
    tableClassName: topConfig.tableClassName,
    calculation: topConfig.tableFunction,
    totalCalc,
    hiddenTOTAL: topConfig.tableCommuteTotal,
  });
}

export function naturalGasRadio(formId) {
  const topConfig = {
    tableClassName: 'building-type__select-nat_gas',
    radioSelectClassName: 'nat-gas-type__select',
    annualFunction: annualNaturalGas,
    selectFunction: naturalGasBuilding,
    annualInput: fields.nat_gas_input.id,
    selectInput: fields.nat_gas_select_input.name,
    annualElementTotal: hiddenValues.annual_nat_gas_total.id,
    selectElementTotal: hiddenValues.select_nat_gas_total.id,
    radioTotal: hiddenValues.nat_gas_total.id,
  };
  radioComponent.confirmationRadioListener({
    className: topConfig.radioSelectClassName,
    hiddenAnnual: topConfig.annualElementTotal,
    hiddenSelect: topConfig.selectElementTotal,
    hiddenTotal: topConfig.radioTotal,
    totalCalc,
  });
  radioComponent.annualListener({
    calculation: topConfig.annualFunction,
    inputID: topConfig.annualInput,
    hiddenID: topConfig.annualElementTotal,
    totalCalc,
  });
  radioComponent.rowListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
  });
  radioComponent.selectListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
  });
}

export function fuelOilRadio(formId) {
  const topConfig = {
    tableClassName: 'building-type__select-fuel_oil',
    radioSelectClassName: 'fuel-oil-type__select',
    annualFunction: annualFuelOil,
    selectFunction: fuelOilBuilding,
    annualInput: fields.fuel_oil_input.id,
    selectInput: fields.fuel_oil_select_input.name,
    annualElementTotal: hiddenValues.annual_fuel_oil_total.id,
    selectElementTotal: hiddenValues.select_fuel_oil_total.id,
    radioTotal: hiddenValues.fuel_oil_total.id,
  };

  radioComponent.confirmationRadioListener({
    className: topConfig.radioSelectClassName,
    hiddenAnnual: topConfig.annualElementTotal,
    hiddenSelect: topConfig.selectElementTotal,
    hiddenTotal: topConfig.radioTotal,
    totalCalc,
  });
  radioComponent.annualListener({
    calculation: topConfig.annualFunction,
    inputID: topConfig.annualInput,
    hiddenID: topConfig.annualElementTotal,
    totalCalc,
  });
  radioComponent.rowListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
  });
  radioComponent.selectListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
  });
}

export function electricityRadio(formId) {
  const topConfig = {
    tableClassName: 'building-type__select-power',
    radioSelectClassName: 'electricity-type__select',
    annualFunction: annualElectricity,
    selectFunction: electricityBuilding,
    recFunction: electricityREC,
    selectRecFunction: electricitySelectREC,
    annualInput: fields.electricity_input.id,
    selectInput: fields.electricity_select_input.name,
    annualElementTotal: hiddenValues.annual_electricity_total.id,
    selectElementTotal: hiddenValues.select_electricity_total.id,
    hiddenElementREC: hiddenValues.rec_electricity.id,
    eGrid: hiddenValues.egrid.id,
    radioTotal: hiddenValues.electricity_total.id,
  };

  radioElectricityComponent.annualListener({
    calculation: topConfig.annualFunction,
    calculationREC: topConfig.recFunction,
    inputID: topConfig.annualInput,
    hiddenAnnual: topConfig.annualElementTotal,
    hiddenGRID: topConfig.eGrid,
    hiddenREC: topConfig.hiddenElementREC,
    totalCalc,
    formId,
  });
  radioElectricityComponent.confirmationRadioListener({
    className: topConfig.radioSelectClassName,
    hiddenAnnual: topConfig.annualElementTotal,
    hiddenSelect: topConfig.selectElementTotal,
    hiddenTotal: topConfig.radioTotal,
    totalCalc,
  });
  radioElectricityComponent.selectListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    calculationREC: topConfig.selectRecFunction,
    hiddenGRID: topConfig.eGrid,
    hiddenREC: topConfig.hiddenElementREC,
    formId,
  });
  radioElectricityComponent.rowListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    calculationREC: topConfig.selectRecFunction,
    hiddenGRID: topConfig.eGrid,
    hiddenREC: topConfig.hiddenElementREC,
    formId,
  });
}

export function waterRadio() {
  const topConfig = {
    tableClassName: 'building-type__select-water',
    radioSelectClassName: 'water-type__select',
    annualFunctionWRC: waterWRC,
    selectFunction: waterBuilding,
    wrcFunction: waterWRC,
    annualInput: fields.water_input.id,
    selectInput: fields.water_select_input.name,
    annualElementTotal: hiddenValues.annual_waterWRC_total.id,
    selectElementTotal: hiddenValues.select_waterWRC_total.id,
    radioTotal: hiddenValues.wrc_water.id,
  };

  radioWaterComponent.annualListener({
    calculationWRC: topConfig.annualFunctionWRC,
    inputID: topConfig.annualInput,
    hiddenWRC: topConfig.annualElementTotal,
    totalCalc,
  });
  radioWaterComponent.confirmationRadioListener({
    className: topConfig.radioSelectClassName,
    hiddenAnnual: topConfig.annualElementTotal,
    hiddenSelect: topConfig.selectElementTotal,
    hiddenTotal: topConfig.radioTotal,
    totalCalc,
  });
  radioWaterComponent.selectListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    calculationWRC: topConfig.wrcFunction,
  });
  radioWaterComponent.rowListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    calculationWRC: topConfig.wrcFunction,
  });
}

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

export function propaneListener() {
  const input = document.getElementById(fields.propane_input.id);

  // hidden total results for natural gas entry
  const hidden = document.getElementById(hiddenValues.propane_total.id);

  input.addEventListener(
    'change',
    () => {
      //  Add that result to the hidden value for this entry
      const inputVal = input.value || '0';
      const result = annualPropane(parseFloat(inputVal.replace(/,/g, '')));
      hidden.value = Number(result).toFixed(2);

      // call function to update total with result
      totalCalc.scope1Calc();
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

export function dieselListener() {
  const input = document.getElementById(fields.diesel_input.id);

  // hidden total results for natural gas entry
  const hidden = document.getElementById(hiddenValues.diesel_total.id);

  input.addEventListener(
    'change',
    () => {
      //  Add that result to the hidden value for this entry
      const inputVal = input.value || '0';
      const result = annualDiesel(parseFloat(inputVal.replace(/,/g, '')));
      hidden.value = Number(result).toFixed(2);

      // call function to update total with result
      totalCalc.scope1Calc();
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

export function offsiteServerListener() {
  const input = document.getElementById(fields.offsiteServer_input.id);

  // hidden total results for natural gas entry
  const hidden = document.getElementById(hiddenValues.offsiteServer_total.id);
  const hiddenREC = document.getElementById(hiddenValues.rec_offsiteServer.id);

  input.addEventListener(
    'change',
    () => {
      const inputVal = input.value || '0';
      //  Add that result to the hidden value for this entry
      const result = offsiteServer(parseFloat(inputVal.replace(/,/g, '')));
      hidden.value = Number(result).toFixed(2);

      // REC calculation
      const rec = offsiteREC(parseFloat(inputVal.replace(/,/g, '')));
      hiddenREC.value = Number(rec).toFixed(2);

      // call function to update total with result
      totalCalc.scope2Calc();
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

export function annualLodgingListener(formId) {
  const input = document.getElementById(fields.lodging_input.id);

  // hidden total results for natural gas entry
  const hidden = document.getElementById(hiddenValues.lodging_total.id);

  input.addEventListener(
    'change',
    () => {
      //  Add that result to the hidden value for this entry
      const inputVal = input.value || '0';
      const result = annualLodging(parseFloat(inputVal.replace(/,/g, '')));
      hidden.value = Number(result).toFixed(2);

      // call function to update total with result
      if (formId === forms.businessFormId) {
        totalCalc.scope3Calc();
      }
      if (formId === forms.householdFormId) {
        totalCalc.scope5Calc();
      }
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

export function annualRentalListener(formId) {
  const input = document.getElementById(fields.rental_input.id);

  // hidden total results for natural gas entry
  const hidden = document.getElementById(hiddenValues.rental_total.id);

  input.addEventListener(
    'change',
    () => {
      //  Add that result to the hidden value for this entry
      const inputVal = input.value || '0';
      const result = annualRentalCar(parseFloat(inputVal.replace(/,/g, '')));
      hidden.value = Number(result).toFixed(2);

      // call function to update total with result
      if (formId === forms.businessFormId) {
        totalCalc.scope3Calc();
      }
      if (formId === forms.householdFormId) {
        totalCalc.scope5Calc();
      }
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

export function getGridFromZip(formId) {
  const input = document.getElementById(fields.zipcode_input.id);

  // hidden result field to store electricity grid entry
  const hiddenEgrid = document.getElementById(hiddenValues.egrid.id);

  // get current totals to see if recalculation is needed
  const hiddenElectricTotal = document.getElementById(hiddenValues.electricity_total.id).value;
  const hiddenElectricTotalParsed = parseFloat(hiddenElectricTotal.replace(/,/g, ''));

  input.addEventListener(
    'change',
    () => {
      //  Add that result to the hidden value for this entry
      //  AJAX call to WP and get appropriate Egrid value from lookup table
      egridAjax(input.value).then((eGrid) => {
        hiddenEgrid.value = eGrid;
        // Recalculate energy calculations.
        // Check if entries exist.  If so, recalculate
        if (hiddenElectricTotalParsed > 0) {
          const hiddenAnnualTotal = document.getElementById(hiddenValues.annual_electricity_total.id);
          const selectHiddenTotal = document.getElementById(hiddenValues.select_electricity_total.id);

          const hiddenRECTotal = document.getElementById(hiddenValues.rec_electricity.id);

          if (hiddenAnnualTotal.value > 0) {
            // Annual total calculation
            const annualValue = document.getElementById(fields.electricity_input.id);
            const inputVal = annualValue.value || '0';

            annualElectricityCalc({
              formId,
              forms,
              inputVal,
              hiddenAnnualTotal,
              hiddenRECTotal,
              hiddenGridCode: eGrid,
              calculation: annualElectricity,
              calculationREC: electricityREC,
              totalCalc,
            });
          }
          if (selectHiddenTotal.value > 0) {
            // Select total calculation
            const el = document.getElementsByName(fields.electricity_select_input.name);

            selectElectricityCalc({
              formId,
              forms,
              el,
              calculation: electricityBuilding,
              calculationREC: electricitySelectREC,
              eGRID: eGrid,
              hidden: selectHiddenTotal,
              hREC: hiddenRECTotal,
              totalCalc,
            });
          }
        }
      });
    },
    false,
  );
}

export function formSubmit(formId) {
  // Grab Form
  const selectedElement = document.getElementById(`gform_submit_${formId}`);

  selectedElement.addEventListener(
    'click',
    () => {
      document.getElementById('gw_page_progression').value = 6;
      document.getElementById(`gform_source_page_number_${formId}`).value = 6;
      document.getElementById(`gform_target_page_number_${formId}`).value = 0;
      document.forms[`gform_${formId}`].submit();
    },
    false,
  );
}
