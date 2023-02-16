import { buildingTypes } from '../constants';

/**
 *
 * @param sqft
 * @param type
 * @returns {number}
 */
export default function electricitySelectREC(sqft, type) {
  let kWh;
  if (type === 'none') {
    kWh = 0;
  } else {
    kWh = sqft * buildingTypes[type].electric_kwh_sqft;
  }
  return kWh / 1000;
}
