import {
  annualFlight,
  annualFuelOil,
  annualLodging,
  annualNaturalGas,
  annualPropane,
  annualRentalCar,
} from '../calculators/basic';
import {
  electricityHousehold, fuelOilHousehold, naturalGasHousehold, propaneHousehold, waterHousehold,
} from '../calculators/dropDown';
import { fields, hiddenValues } from './fieldmap';
import * as radioComponent from '../components/radioComponent';
import * as radioDietComponent from '../components/radioDietComponent';
import * as totalCalc from './totalsHousehold';
import { annualElectricity } from '../calculators/complexElectricty';
import { dietType } from '../calculators/complexFood';
import { electricityREC, electricityHouseholdSelectREC, waterWRC } from '../calculators/rec_wrc';
import * as radioElectricityComponent from '../components/radioElectricityComponent';
import { egridAjax } from '../calculators/utility';
import * as radioWaterHouseholdComponent from '../components/radioWaterHouseholdComponent';
import { personalVehicleFleet } from '../calculators/complexCommute';
import * as tablePersonalVehicle from '../components/tablePersonalVehicle';
import * as radioFlight from '../components/radioFlightComponent';
import { flightTable } from '../calculators/complexTravel';
import * as tableFlightComponent from '../components/tableFlightComponent';

import { forms } from '../calculators/constants';
import { annualElectricityCalc } from '../components/electricityFunctions/annualElectricityCalc';
import { selectElectricityCalc } from '../components/electricityFunctions/selectElectricityCalc';

export function naturalGasRadio(formId) {
  const topConfig = {
    tableClassName: 'household-type__select-nat_gas',
    radioSelectClassName: 'nat-gas-type__select',
    annualFunction: annualNaturalGas,
    selectFunction: naturalGasHousehold,
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
    tableClassName: 'household-type__select-fuel_oil',
    radioSelectClassName: 'fuel-oil-type__select',
    annualFunction: annualFuelOil,
    selectFunction: fuelOilHousehold,
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
    tableClassName: 'household-type__select-power',
    radioSelectClassName: 'electricity-type__select',
    annualFunction: annualElectricity,
    selectFunction: electricityHousehold,
    recFunction: electricityREC,
    selectRecFunction: electricityHouseholdSelectREC,
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
    formId,
    totalCalc,
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

export function propaneRadio(formId) {
  const topConfig = {
    tableClassName: 'household-type__select-propane',
    radioSelectClassName: 'propane-type__select',
    annualFunction: annualPropane,
    selectFunction: propaneHousehold,
    annualInput: fields.propane_input.id,
    selectInput: fields.propane_select_input.name,
    annualElementTotal: hiddenValues.annual_propane_total.id,
    selectElementTotal: hiddenValues.select_propane_total.id,
    radioTotal: hiddenValues.propane_total.id,
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

export function waterRadio() {
  const topConfig = {
    radioSelectClassName: 'water-type__select',
    annualFunctionWRC: waterWRC,
    selectFunction: waterHousehold,
    annualInput: fields.water_input.id,
    selectInput: fields.water_select_input.id,
    annualElementTotal: hiddenValues.annual_water_total.id,
    selectElementTotal: hiddenValues.select_water_total.id,
    radioTotal: hiddenValues.water_total.id,
    annualWRCElementTotal: hiddenValues.annual_waterWRC_total.id,
    selectWRCElementTotal: hiddenValues.select_waterWRC_total.id,
    radioWRCTotal: hiddenValues.wrc_water.id,
  };

  radioWaterHouseholdComponent.annualListener({
    calculationWRC: topConfig.annualFunctionWRC,
    inputID: topConfig.annualInput,
    hiddenWRC: topConfig.annualWRCElementTotal,
    hiddenAnnualTotal: topConfig.annualElementTotal,
    totalCalc,
  });
  radioWaterHouseholdComponent.householdAveListener({
    calculation: topConfig.selectFunction,
    resultWRC: topConfig.annualFunctionWRC,
    inputID: topConfig.selectInput,
    hiddenSelectTotal: topConfig.selectElementTotal,
    hiddenWRC: topConfig.selectWRCElementTotal,
    totalCalc,
  });
  radioWaterHouseholdComponent.confirmationRadioListener({
    className: topConfig.radioSelectClassName,
    hiddenAnnual: topConfig.annualElementTotal,
    hiddenSelect: topConfig.selectElementTotal,
    hiddenTotal: topConfig.radioTotal,
    annualWRCElementTotal: topConfig.annualWRCElementTotal,
    selectWRCElementTotal: topConfig.selectWRCElementTotal,
    radioWRCTotal: topConfig.radioWRCTotal,
    totalCalc,
  });
}

export function personalVehicleTable() {
  const topConfig = {
    tableClassName: 'table__personal-vehicle',
    tableFunction: personalVehicleFleet,
    hiddenCarTotal: hiddenValues.car_total.id,
    hiddenHybridTotal: hiddenValues.hybrid_car_total.id,
    hiddenElectricVehicleTotal: hiddenValues.electric_vehicle_total.id,
    hiddenPickupTruckTotal: hiddenValues.pickup_truck_van_total.id,
    tableCommuteTotal: hiddenValues.personalVehicle_total.id,
  };
  tablePersonalVehicle.personalVehicleListener({
    tableClassName: topConfig.tableClassName,
    calculation: topConfig.tableFunction,
    totalCalc,
    hiddenTOTAL: topConfig.tableCommuteTotal,
    hiddenCarTotal: topConfig.hiddenCarTotal,
    hiddenHybridTotal: topConfig.hiddenHybridTotal,
    hiddenElectricVehicleTotal: topConfig.hiddenElectricVehicleTotal,
    hiddenPickupTruckTotal: topConfig.hiddenPickupTruckTotal,
  });
}

export function personalDietTable(formId) {
  const topConfig = {
    tableClassName: 'household-type__select-diet',
    radioSelectClassName: 'diet-type__select',
    selectFunction: dietType,
    selectInput: fields.diet_select_input.name,
    selectElementTotal: hiddenValues.select_diet_total.id,
    radioTotal: hiddenValues.diet_total.id,
  };
  radioDietComponent.confirmationRadioListener({
    className: topConfig.radioSelectClassName,
    hiddenSelect: topConfig.selectElementTotal,
    hiddenTotal: topConfig.radioTotal,
    totalCalc,
  });
  radioDietComponent.rowListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
  });
  radioDietComponent.selectListener({
    className: topConfig.tableClassName,
    elementName: topConfig.selectInput,
    selectElementResult: topConfig.selectElementTotal,
    totalCalc,
    calculation: topConfig.selectFunction,
    formId,
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
        totalCalc.scope4Calc();
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
        totalCalc.scope4Calc();
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
              calculation: electricityHousehold,
              calculationREC: electricityHouseholdSelectREC,
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
      document.getElementById(`gform_source_page_number_${formId}`).value = 6;
      document.getElementById(`gform_target_page_number_${formId}`).value = 0;
      document.forms[`gform_${formId}`].submit();
    },
    false,
  );
}
