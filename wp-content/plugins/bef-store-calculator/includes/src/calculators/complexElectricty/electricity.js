import { conversionFactor, egridFactors } from '../constants';

/**
 *
 * @param annualUsage
 * @param eGrid
 * @returns {number}
 */
export default function annualElectricity(annualUsage, eGrid) {
  const c1 = egridFactors[eGrid].carbon_dioxide_lb_mwh / 2.20462 / 1000;
  const c2 = egridFactors[eGrid].methane_lb_gwh / 2.20462;
  const c3 = egridFactors[eGrid].nitrous_oxide_factor_lb_gwh / 2.20462;
  const c4 = (annualUsage * c1) / conversionFactor.kg_mt.value;
  const c5 = (annualUsage * c2) / conversionFactor.kg_mt.value;
  const c6 = (annualUsage * c3) / conversionFactor.kg_mt.value;
  const emission = c4 + (c5 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c6 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
    / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;
  return emission;
}
