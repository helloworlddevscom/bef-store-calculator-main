// Summarize all the hidden fields for the total calculation
import { hiddenValues } from './fieldmap';
import { totalEmissionFormat } from '../calculators/utility';

export function emissionTotalCalc() {
  const totalResult = document.getElementById('report-menu__emissions--result');
  const totalHidden = document.getElementById(hiddenValues.household_total.id);

  // Natural Gas Totals
  const AnnualNatGasTotal = document.getElementById(hiddenValues.annual_nat_gas_total.id).value;
  const AnnualNatGasTotalParsed = parseFloat(AnnualNatGasTotal.replace(/,/g, ''));

  const SelectNatGasTotal = document.getElementById(hiddenValues.select_nat_gas_total.id).value;
  const SelectNatGasTotalParsed = parseFloat(SelectNatGasTotal.replace(/,/g, ''));

  const natGasTotalParsed = AnnualNatGasTotalParsed + SelectNatGasTotalParsed;
  const natGasTotal = document.getElementById(hiddenValues.nat_gas_total.id);
  natGasTotal.value = natGasTotalParsed;

  // Fuel Oil Totals
  const AnnualFuelOilTotal = document.getElementById(hiddenValues.annual_fuel_oil_total.id).value;
  const AnnualFuelOilTotalParsed = parseFloat(AnnualFuelOilTotal.replace(/,/g, ''));

  const SelectFuelOilTotal = document.getElementById(hiddenValues.select_fuel_oil_total.id).value;
  const SelectFuelOilTotalParsed = parseFloat(SelectFuelOilTotal.replace(/,/g, ''));

  const fuelOilTotalParsed = AnnualFuelOilTotalParsed + SelectFuelOilTotalParsed;
  const fuelOilTotal = document.getElementById(hiddenValues.fuel_oil_total.id);
  fuelOilTotal.value = fuelOilTotalParsed;

  // Propane Totals
  const AnnualPropaneTotal = document.getElementById(hiddenValues.annual_propane_total.id).value;
  const AnnualPropaneTotalParsed = parseFloat(AnnualPropaneTotal.replace(/,/g, ''));

  const SelectPropaneTotal = document.getElementById(hiddenValues.select_propane_total.id).value;
  const SelectPropaneTotalParsed = parseFloat(SelectPropaneTotal.replace(/,/g, ''));

  const propaneTotalParsed = AnnualPropaneTotalParsed + SelectPropaneTotalParsed;
  const propaneTotal = document.getElementById(hiddenValues.propane_total.id);
  propaneTotal.value = propaneTotalParsed;

  //
  const personalVehicleTotal = document.getElementById(hiddenValues.personalVehicle_total.id).value;
  const personalVehicleParsed = parseFloat(personalVehicleTotal.replace(/,/g, ''));

  // Diet Total
  const dietTotal = document.getElementById(hiddenValues.diet_total.id).value;
  const dietTotalParsed = parseFloat(dietTotal.replace(/,/g, ''));

  // Electricity Totals
  const AnnualElectricityTotal = document.getElementById(hiddenValues.annual_electricity_total.id).value;
  const AnnualElectricityTotalParsed = parseFloat(AnnualElectricityTotal.replace(/,/g, ''));

  const SelectElectricityTotal = document.getElementById(hiddenValues.select_electricity_total.id).value;
  const SelectElectricityTotalParsed = parseFloat(SelectElectricityTotal.replace(/,/g, ''));

  const electricityTotalParsed = AnnualElectricityTotalParsed + SelectElectricityTotalParsed;
  const electricityTotal = document.getElementById(hiddenValues.electricity_total.id);
  electricityTotal.value = electricityTotalParsed;

  // Flight Totals
  const AnnualFlightTotal = document.getElementById(hiddenValues.annual_flight_total.id).value;
  const AnnualFlightTotalParsed = parseFloat(AnnualFlightTotal.replace(/,/g, ''));

  const SelectFlightTotal = document.getElementById(hiddenValues.select_flight_total.id).value;
  const SelectFlightTotalParsed = parseFloat(SelectFlightTotal.replace(/,/g, ''));

  const TableFlightTotal = document.getElementById(hiddenValues.table_flight_total.id).value;
  const TableFlightTotalParsed = parseFloat(TableFlightTotal.replace(/,/g, ''));

  const FlightTotalParsed = AnnualFlightTotalParsed + SelectFlightTotalParsed + TableFlightTotalParsed;
  const FlightTotal = document.getElementById(hiddenValues.flight_total.id);
  FlightTotal.value = FlightTotalParsed;
  //
  // // Lodging summary
  const lodgingTotal = document.getElementById(hiddenValues.lodging_total.id).value;
  const lodgingParsed = parseFloat(lodgingTotal.replace(/,/g, ''));
  //
  // // Rental summary
  const rentalTotal = document.getElementById(hiddenValues.rental_total.id).value;
  const rentalParsed = parseFloat(rentalTotal.replace(/,/g, ''));
  //
  // // WRC Totals
  const AnnualWRCTotal = document.getElementById(hiddenValues.annual_waterWRC_total.id).value;
  const AnnualWRCTotalParsed = parseFloat(AnnualWRCTotal.replace(/,/g, ''));

  const SelectWRCTotal = document.getElementById(hiddenValues.select_waterWRC_total.id).value;
  const SelectWRCTotalParsed = parseFloat(SelectWRCTotal.replace(/,/g, ''));

  const WRCTotalParsed = AnnualWRCTotalParsed + SelectWRCTotalParsed;
  const WRCTotal = document.getElementById(hiddenValues.wrc_water.id);
  WRCTotal.value = WRCTotalParsed;

  // // Water Totals
  const AnnualTotal = document.getElementById(hiddenValues.annual_water_total.id).value;
  const AnnualTotalParsed = parseFloat(AnnualTotal.replace(/,/g, ''));

  const SelectTotal = document.getElementById(hiddenValues.select_water_total.id).value;
  const SelectTotalParsed = parseFloat(SelectTotal.replace(/,/g, ''));

  const waterTotalParsed = AnnualTotalParsed + SelectTotalParsed;
  const waterTotal = document.getElementById(hiddenValues.water_total.id);
  waterTotal.value = waterTotalParsed;

  const totalEntry = [
    natGasTotalParsed,
    fuelOilTotalParsed,
    electricityTotalParsed,
    propaneTotalParsed,
    personalVehicleParsed,
    FlightTotalParsed,
    lodgingParsed,
    rentalParsed,
    dietTotalParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);
  totalHidden.value = Number(result).toFixed(2);

  // push to display result
  totalResult.innerText = totalEmissionFormat(result);
}

// Summarize all the hidden fields for the Scope1 calculation
export function scope1Calc() {
  const totalHidden = document.getElementById(hiddenValues.scope1_total.id);

  // Natural Gas Totals
  const AnnualNatGasTotal = document.getElementById(hiddenValues.annual_nat_gas_total.id).value;
  const AnnualNatGasTotalParsed = parseFloat(AnnualNatGasTotal.replace(/,/g, ''));

  const SelectNatGasTotal = document.getElementById(hiddenValues.select_nat_gas_total.id).value;
  const SelectNatGasTotalParsed = parseFloat(SelectNatGasTotal.replace(/,/g, ''));

  const natGasTotalParsed = AnnualNatGasTotalParsed + SelectNatGasTotalParsed;
  const natGasTotal = document.getElementById(hiddenValues.nat_gas_total.id);
  natGasTotal.value = natGasTotalParsed;

  // Fuel Oil Totals
  const AnnualFuelOilTotal = document.getElementById(hiddenValues.annual_fuel_oil_total.id).value;
  const AnnualFuelOilTotalParsed = parseFloat(AnnualFuelOilTotal.replace(/,/g, ''));

  const SelectFuelOilTotal = document.getElementById(hiddenValues.select_fuel_oil_total.id).value;
  const SelectFuelOilTotalParsed = parseFloat(SelectFuelOilTotal.replace(/,/g, ''));

  const fuelOilTotalParsed = AnnualFuelOilTotalParsed + SelectFuelOilTotalParsed;
  const fuelOilTotal = document.getElementById(hiddenValues.fuel_oil_total.id);
  fuelOilTotal.value = fuelOilTotalParsed;

  // Electricity Totals
  const AnnualElectricityTotal = document.getElementById(hiddenValues.annual_electricity_total.id).value;
  const AnnualElectricityTotalParsed = parseFloat(AnnualElectricityTotal.replace(/,/g, ''));

  const SelectElectricityTotal = document.getElementById(hiddenValues.select_electricity_total.id).value;
  const SelectElectricityTotalParsed = parseFloat(SelectElectricityTotal.replace(/,/g, ''));

  const electricityTotalParsed = AnnualElectricityTotalParsed + SelectElectricityTotalParsed;
  const electricityTotal = document.getElementById(hiddenValues.electricity_total.id);
  electricityTotal.value = electricityTotalParsed;

  // Propane Totals
  const AnnualPropaneTotal = document.getElementById(hiddenValues.annual_propane_total.id).value;
  const AnnualPropaneTotalParsed = parseFloat(AnnualPropaneTotal.replace(/,/g, ''));

  const SelectPropaneTotal = document.getElementById(hiddenValues.select_propane_total.id).value;
  const SelectPropaneTotalParsed = parseFloat(SelectPropaneTotal.replace(/,/g, ''));

  const propaneTotalParsed = AnnualPropaneTotalParsed + SelectPropaneTotalParsed;
  const propaneTotal = document.getElementById(hiddenValues.propane_total.id);
  propaneTotal.value = propaneTotalParsed;

  const totalEntry = [
    natGasTotalParsed,
    fuelOilTotalParsed,
    electricityTotalParsed,
    propaneTotalParsed,
    // ownedVehicleParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);

  totalHidden.value = Number(result).toFixed(2);
}

// Summarize all the hidden fields for the Scope1 calculation
export function scope3Calc() {
  const totalHidden = document.getElementById(hiddenValues.scope3_total.id);

  const personalVehicleTotal = document.getElementById(hiddenValues.personalVehicle_total.id).value;
  const personalVehicleParsed = parseFloat(personalVehicleTotal.replace(/,/g, ''));

  const totalEntry = [
    personalVehicleParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);

  totalHidden.value = Number(result).toFixed(2);
}

// Summarize all the hidden fields for the Scope4 calculation
export function scope4Calc() {
  const totalHidden = document.getElementById(hiddenValues.scope4_total.id);

  // Lodging summary
  const lodgingTotal = document.getElementById(hiddenValues.lodging_total.id).value;
  const lodgingParsed = parseFloat(lodgingTotal.replace(/,/g, ''));
  //
  // // Rental summary
  const rentalTotal = document.getElementById(hiddenValues.rental_total.id).value;
  const rentalParsed = parseFloat(rentalTotal.replace(/,/g, ''));

  // Flight Totals
  const AnnualFlightTotal = document.getElementById(hiddenValues.annual_flight_total.id).value;
  const AnnualFlightTotalParsed = parseFloat(AnnualFlightTotal.replace(/,/g, ''));

  const SelectFlightTotal = document.getElementById(hiddenValues.select_flight_total.id).value;
  const SelectFlightTotalParsed = parseFloat(SelectFlightTotal.replace(/,/g, ''));

  const TableFlightTotal = document.getElementById(hiddenValues.table_flight_total.id).value;
  const TableFlightTotalParsed = parseFloat(TableFlightTotal.replace(/,/g, ''));

  const FlightTotalParsed = AnnualFlightTotalParsed + SelectFlightTotalParsed + TableFlightTotalParsed;
  const FlightTotal = document.getElementById(hiddenValues.flight_total.id);
  FlightTotal.value = FlightTotalParsed;

  const totalEntry = [
    FlightTotalParsed,
    lodgingParsed,
    rentalParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);

  totalHidden.value = Number(result).toFixed(2);
}

// Summarize all the hidden fields for the Scope4 calculation
export function scope5Calc() {
  const totalHidden = document.getElementById(hiddenValues.scope5_total.id);

  // Diet Select Total
  const SelectDietTotal = document.getElementById(hiddenValues.select_diet_total.id).value;
  const SelectDietTotalParsed = parseFloat(SelectDietTotal.replace(/,/g, ''));

  // Diet Total -- Only Select total, so copying.  Placeholder setup like other
  // calculations incase more options are asked for later
  const dietTotal = document.getElementById(hiddenValues.diet_total.id);
  dietTotal.value = SelectDietTotalParsed;
  const dietTotalParsed = parseFloat(SelectDietTotal.replace(/,/g, ''));

  const totalEntry = [
    dietTotalParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);

  totalHidden.value = Number(result).toFixed(2);
}
