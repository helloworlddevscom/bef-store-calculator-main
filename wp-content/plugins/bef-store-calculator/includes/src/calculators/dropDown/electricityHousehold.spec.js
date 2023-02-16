import electricityHousehold from './electricityHousehold';

describe('Annual electricity Usage (kWh)', () => {
  it('calculates mT from annual electricity usage household in kWh', () => {
    const householdType = 'sqft_1500_1999';
    const eGrid = 'NWPP';
    const result = 3.3960528009108146;
    expect(electricityHousehold(householdType, eGrid)).toEqual(result);
  });
});
