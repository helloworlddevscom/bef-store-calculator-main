import { conversionFactor, emissionTypes } from '../constants';

/**
 *
 * @param annualUsage
 * @returns number
 */
export default function annualPropane(annualUsage) {
  const c1 = annualUsage * emissionTypes.propane_carbon_dioxide.value / conversionFactor.kg_mt.value;
  const c2 = emissionTypes.propane_methane.value * conversionFactor.l_gal.value * conversionFactor.g_kg.value;
  const c3 = emissionTypes.propane_nitrious_oxide.value * conversionFactor.l_gal.value * conversionFactor.g_kg.value;
  const c4 = c2 * annualUsage;
  const c5 = c3 * annualUsage;
  const emission = c1 + (c4 * conversionFactor.mt_carbon_dioxide_mt_methane.value
    + c5 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value) / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;

  // console.log({
  //   annualUsage, c1, c2, c3, c4, c5, emission,
  // });
  return emission;
}
