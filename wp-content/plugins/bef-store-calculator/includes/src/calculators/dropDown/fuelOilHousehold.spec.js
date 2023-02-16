import fuelOilHousehold from './fuelOilHousehold';

describe('CO2 emission for fuel oil from Household type', () => {
  it('calculates mT from fuel oil Household type input', () => {
    const HouseholdType = 'sqft_3000';
    const result = 6.4;
    expect(fuelOilHousehold(HouseholdType)).toEqual(result);
  });
});
