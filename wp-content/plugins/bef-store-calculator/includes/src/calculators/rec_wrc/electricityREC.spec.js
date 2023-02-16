import electricityREC from './electricityREC';

describe('REC calculation for electricity usage', () => {
  it('calculates REC for annual electricity usage in kWh', () => {
    const input = 10000;
    const result = 10;
    expect(electricityREC(input)).toEqual(result);
  });
});