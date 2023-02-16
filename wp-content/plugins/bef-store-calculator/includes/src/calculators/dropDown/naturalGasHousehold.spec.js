import naturalGasHousehold from './naturalGasHousehold';

describe('CO2 emission for natural gas from building type', () => {
  it('calculates mT from natural gas building type input', () => {
    const householdType = 'sqft_2000_2499';
    const result = 3.649;
    expect(naturalGasHousehold(householdType)).toEqual(result);
  });
});