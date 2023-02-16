import { conversionFactor, emissionTypes } from '../constants';

/**
 *
 * @param annualUsage
 * @returns number
 */
export default function annualFuelOil(annualUsage) {
  const c1 = (annualUsage * emissionTypes.heating_oil_carbon_dioxide.value) / conversionFactor.kg_mt.value;
  const c2 = emissionTypes.heating_oil_methane.value * conversionFactor.tj_mmbtu.value * conversionFactor.g_kg.value;
  const c3 = c2 / conversionFactor.btu_mmbtu.value * conversionFactor.btu_gal_diesel.value;
  const c4 = c3 * annualUsage;
  const c5 = emissionTypes.heating_oil_nitrious_oxide.value * conversionFactor.tj_mmbtu.value * conversionFactor.kg_mt.value;
  const c6 = c5 / conversionFactor.btu_mmbtu.value * conversionFactor.btu_gal_diesel.value;
  const c7 = c6 * annualUsage;
  const emission = c1 + (c4 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c7 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value) / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;

  // console.log({
  //   annualUsage, c1, c2, c3, c4, c5, c6, c7, emission,
  // });
  return emission;
}
