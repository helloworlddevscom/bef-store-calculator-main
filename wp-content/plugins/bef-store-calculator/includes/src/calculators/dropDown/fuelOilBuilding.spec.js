import fuelOilBuilding from './fuelOilBuilding';

describe('CO2 emission for fuel oil from building type', () => {
  it('calculates mT from fuel oil building type input', () => {
    const input = 5000;
    const buildingType = 'outpatient_health_care';
    const result = 2.55675;
    expect(fuelOilBuilding(input, buildingType)).toEqual(result);
  });
});