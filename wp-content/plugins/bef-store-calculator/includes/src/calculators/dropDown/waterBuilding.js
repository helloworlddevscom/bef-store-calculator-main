import { buildingTypes } from '../constants';

/**
 *
 * @param sqft
 * @param type
 * @returns {number}
 */
export default function waterBuilding(sqft, type) {
  let c1;
  if (type === 'none') {
    c1 = 0;
  } else {
    c1 = sqft * buildingTypes[type].water_gallon_sqft;
  }
  return c1;
}
