import { buildingTypes } from '../constants';

/**
 *
 * @param sqft
 * @param type
 * @returns {number}
 */
export default function fuelOilBuilding(sqft, type) {
  let c1;
  if (type === 'none') {
    c1 = 0;
  } else {
    c1 = sqft * buildingTypes[type].fuel_oil_gallon_sqft;
  }
  return c1 * 0.010227;
}
