// Summarize all the hidden fields for the total calculation
import { hiddenValues } from './fieldmap';
import { totalEmissionFormat } from '../calculators/utility';

export function emissionTotalCalc() {
  const totalResult = document.getElementById('report-menu__emissions--result');
  const totalHidden = document.getElementById(hiddenValues.business_total.id);

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

  const propaneTotal = document.getElementById(hiddenValues.propane_total.id).value;
  const propaneTotalParsed = parseFloat(propaneTotal.replace(/,/g, ''));

  const dieselTotal = document.getElementById(hiddenValues.diesel_total.id).value;
  const dieselTotalParsed = parseFloat(dieselTotal.replace(/,/g, ''));

  const ownedVehicleTotal = document.getElementById(hiddenValues.ownedVehicle_total.id).value;
  const ownedVehicleParsed = parseFloat(ownedVehicleTotal.replace(/,/g, ''));

  const offsiteServerTotal = document.getElementById(hiddenValues.offsiteServer_total.id).value;
  const offsiteServerParsed = parseFloat(offsiteServerTotal.replace(/,/g, ''));

  // Electricity Totals
  const AnnualElectricityTotal = document.getElementById(hiddenValues.annual_electricity_total.id).value;
  const AnnualElectricityTotalParsed = parseFloat(AnnualElectricityTotal.replace(/,/g, ''));

  const SelectElectricityTotal = document.getElementById(hiddenValues.select_electricity_total.id).value;
  const SelectElectricityTotalParsed = parseFloat(SelectElectricityTotal.replace(/,/g, ''));

  const electricityTotalParsed = AnnualElectricityTotalParsed + SelectElectricityTotalParsed;
  const electricityTotal = document.getElementById(hiddenValues.electricity_total.id);
  electricityTotal.value = electricityTotalParsed;

  // WRC Totals
  const AnnualWRCTotal = document.getElementById(hiddenValues.annual_waterWRC_total.id).value;
  const AnnualWRCTotalParsed = parseFloat(AnnualWRCTotal.replace(/,/g, ''));

  const SelectWRCTotal = document.getElementById(hiddenValues.select_waterWRC_total.id).value;
  const SelectWRCTotalParsed = parseFloat(SelectWRCTotal.replace(/,/g, ''));

  const WRCTotalParsed = AnnualWRCTotalParsed + SelectWRCTotalParsed;
  const WRCTotal = document.getElementById(hiddenValues.wrc_water.id);
  WRCTotal.value = WRCTotalParsed;

  const commutingTotal = document.getElementById(hiddenValues.commuting_total.id).value;
  const commutingParsed = parseFloat(commutingTotal.replace(/,/g, ''));

  const totalEntry = [
    natGasTotalParsed,
    fuelOilTotalParsed,
    propaneTotalParsed,
    dieselTotalParsed,
    ownedVehicleParsed,
    commutingParsed,
    offsiteServerParsed,
    electricityTotalParsed,
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

  // Propane Totals
  const propaneTotal = document.getElementById(hiddenValues.propane_total.id).value;
  const propaneTotalParsed = parseFloat(propaneTotal.replace(/,/g, ''));

  // Diesel Totals
  const dieselTotal = document.getElementById(hiddenValues.diesel_total.id).value;
  const dieselTotalParsed = parseFloat(dieselTotal.replace(/,/g, ''));

  // owned Vehicle table Totals
  const ownedVehicleTotal = document.getElementById(hiddenValues.ownedVehicle_total.id).value;
  const ownedVehicleParsed = parseFloat(ownedVehicleTotal.replace(/,/g, ''));

  const totalEntry = [
    natGasTotalParsed,
    fuelOilTotalParsed,
    propaneTotalParsed,
    dieselTotalParsed,
    ownedVehicleParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);

  totalHidden.value = Number(result).toFixed(2);
}

// Summarize all the hidden fields for the Scope2 calculation
export function scope2Calc() {
  const totalHidden = document.getElementById(hiddenValues.scope2_total.id);

  const offsiteServerTotal = document.getElementById(hiddenValues.offsiteServer_total.id).value;
  const offsiteServerParsed = parseFloat(offsiteServerTotal.replace(/,/g, ''));

  // Electricity Totals
  const AnnualElectricityTotal = document.getElementById(hiddenValues.annual_electricity_total.id).value;
  const AnnualElectricityTotalParsed = parseFloat(AnnualElectricityTotal.replace(/,/g, ''));

  const SelectElectricityTotal = document.getElementById(hiddenValues.select_electricity_total.id).value;
  const SelectElectricityTotalParsed = parseFloat(SelectElectricityTotal.replace(/,/g, ''));

  const electricityTotalParsed = AnnualElectricityTotalParsed + SelectElectricityTotalParsed;
  const electricityTotal = document.getElementById(hiddenValues.electricity_total.id);
  electricityTotal.value = electricityTotalParsed;

  const totalEntry = [
    offsiteServerParsed,
    electricityTotalParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);

  totalHidden.value = Number(result).toFixed(2);
}

// Summarize all the hidden fields for the Scope1 calculation
export function scope3Calc() {
  const totalHidden = document.getElementById(hiddenValues.scope3_total.id);

  const commutingTotal = document.getElementById(hiddenValues.commuting_total.id).value;
  const commutingParsed = parseFloat(commutingTotal.replace(/,/g, ''));

  const totalEntry = [
    commutingParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);

  totalHidden.value = Number(result).toFixed(2);
}
