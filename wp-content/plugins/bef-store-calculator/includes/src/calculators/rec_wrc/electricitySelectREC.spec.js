import electricitySelectREC from './electricitySelectREC';

describe('REC calculation for electricity usage for buildings', () => {
  it('calculates REC for annual electricity usage for buildings in kWh', () => {
    const input = 5000;
    const buildingType = 'bar_pub_lounge';
    const result = 131.5;
    expect(electricitySelectREC(input, buildingType)).toEqual(result);
  });
});