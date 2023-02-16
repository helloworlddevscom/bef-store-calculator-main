import propaneHousehold from './propaneHousehold';

describe('CO2 emission for propane from household type', () => {
  it('calculates mT from propane household type input', () => {
    const householdType = 'sqft_1000';
    const result = 1.0;
    expect(propaneHousehold(householdType)).toEqual(result);
  });
});