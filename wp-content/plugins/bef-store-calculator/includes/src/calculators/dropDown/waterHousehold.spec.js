import waterHousehold from './waterHousehold';

describe('CO2 water usage from household type', () => {
  it('calculates total water usage from household average input', () => {
    const input = 3;
    const result = 98550;
    expect(waterHousehold(input)).toEqual(result);
  });
});