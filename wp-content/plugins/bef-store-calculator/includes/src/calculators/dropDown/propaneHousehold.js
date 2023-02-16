import { householdTypes } from '../constants';

/**
 *
 * @param type
 * @returns {number}
 */
export default function propaneHousehold(type) {
  let c1;
  if (type === 'none') {
    c1 = 0;
  } else {
    c1 = householdTypes[type].propane_mt_sqft;
  }
  return c1;
}
