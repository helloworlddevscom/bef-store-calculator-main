import { conversionFactor, emissionTypes } from '../constants';

/**
 *
 * @param annualUsage
 * @returns number
 */
export default function annualNaturalGas(annualUsage) {
  const c1 = (annualUsage * conversionFactor.ft_therms.value) / 1000;
  const c2 = (emissionTypes.nat_gas_carbon_dioxide.value / conversionFactor.ccf_m.value) * 1000;
  const c3 = (emissionTypes.nat_gas_methane.value / conversionFactor.ccf_m.value) * 1000 * conversionFactor.g_kg.value;
  const c4 = (emissionTypes.nat_gas_nitrious_oxide.value / conversionFactor.ccf_m.value) * 1000 * conversionFactor.g_kg.value;
  const c5 = (c1 * c2) / conversionFactor.kg_mt.value;
  const c6 = (c1 * c3);
  const c7 = (c1 * c4);
  const emission = c5 + (c6 * conversionFactor.mt_carbon_dioxide_mt_methane.value
      + c7 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
      / conversionFactor.g_kg.value
      / conversionFactor.kg_mt.value;
  // console.log({
  //   annualUsage, c1, c2, c3, c4, c5, c6, c7, emission,
  // });
  return emission;
}
