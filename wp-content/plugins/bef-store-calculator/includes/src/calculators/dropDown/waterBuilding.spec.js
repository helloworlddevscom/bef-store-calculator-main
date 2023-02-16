import waterBuilding from './waterBuilding';

describe('CO2 water usage from building type', () => {
  it('calculates total water usage from  building type input', () => {
    const input = 5000;
    const buildingType = 'bar_pub_lounge';
    const result = 124500;
    expect(waterBuilding(input, buildingType)).toEqual(result);
  });
});