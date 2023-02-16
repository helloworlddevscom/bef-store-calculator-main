import { buildingTypes } from '../constants';

/**
 *
 * @param sqft
 * @param type
 * @returns {number}
 */
export default function naturalGasBuilding(sqft, type) {
  let c1;
  if (type === 'none') {
    c1 = 0;
  } else {
    c1 = buildingTypes[type].nat_gas_ccf_sqft;
  }
  const emission = c1 * sqft * 0.00549;
  return emission;
}
