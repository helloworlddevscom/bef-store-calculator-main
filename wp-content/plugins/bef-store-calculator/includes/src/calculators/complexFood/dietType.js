import { dietTypes } from '../constants';

/**
 *
 * @param selection
 * @returns {number}
 */
export default function dietType(selection) {
  const defaultSelection = {
    dietType: 'none',
    mealsPerDay: 0,
    NumPeople: 0,
  };

  const dietResult = {};
  Object.keys(selection).forEach((key) => {
    if (Number.isNaN(selection[key])) {
      dietResult[key] = defaultSelection[key];
    } else {
      dietResult[key] = selection[key];
    }
    return dietResult;
  });

  let c1;
  if (dietResult.dietType === 'none') {
    c1 = 0;
  } else {
    c1 = dietTypes[dietResult.dietType].carbon_dioxide_meal_day;
  }
  const c2 = c1 * 365 * dietResult.mealsPerDay * dietResult.NumPeople;
  return c2 / 2204.6;
}
