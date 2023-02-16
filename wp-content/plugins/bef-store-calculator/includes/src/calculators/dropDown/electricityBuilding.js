import {
  conversionFactor, buildingTypes, egridFactors,
} from '../constants';

/**
 *
 * @param sqft
 * @param type
 * @param eGrid
 * @returns {number}
 */
export default function electricityBuilding(sqft, type, eGrid) {
  let c1;
  if (type === 'none') {
    c1 = 0;
  } else {
    c1 = sqft * buildingTypes[type].electric_kwh_sqft;
  }
  const c2 = egridFactors[eGrid].carbon_dioxide_lb_mwh / 2.20462 / 1000;
  const c3 = egridFactors[eGrid].methane_lb_gwh / 2.20462;
  const c4 = egridFactors[eGrid].nitrous_oxide_factor_lb_gwh / 2.20462;
  const c5 = (c1 * c2) / conversionFactor.kg_mt.value;
  const c6 = (c1 * c3) / conversionFactor.kg_mt.value;
  const c7 = (c1 * c4) / conversionFactor.kg_mt.value;
  const emission = c5 + (c6 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c7 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
    / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;
  return emission;
}
