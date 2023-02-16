import { householdTypes } from '../constants';

/**
 *
 * @param type
 * @returns {number}
 */
export default function electricityHouseholdSelectREC(type) {
  let kWh;
  if (type === 'none') {
    kWh = 0;
  } else {
    kWh = householdTypes[type].electric_kwh_sqft;
  }
  return kWh / 1000;
}
