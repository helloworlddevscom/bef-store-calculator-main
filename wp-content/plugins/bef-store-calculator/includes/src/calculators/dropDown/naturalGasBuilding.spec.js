import naturalGasBuilding from './naturalGasBuilding';

describe('CO2 emission for natural gas from building type', () => {
  it('calculates mT from natural gas building type input', () => {
    const input = 5000;
    const buildingType = 'grocery_convenience';
    const result = 16.82685;
    expect(naturalGasBuilding(input, buildingType)).toEqual(result);
  });
});