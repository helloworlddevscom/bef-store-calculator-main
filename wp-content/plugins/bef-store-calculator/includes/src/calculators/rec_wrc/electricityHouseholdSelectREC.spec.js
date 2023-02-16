import electricityHouseholdSelectREC from './electricityHouseholdSelectREC';

describe('REC calculation for electricity usage for household', () => {
  it('calculates REC for annual electricity usage for household in kWh', () => {
    const householdType = 'sqft_1500_1999';
    const result = 11.716;
    expect(electricityHouseholdSelectREC(householdType)).toEqual(result);
  });
});