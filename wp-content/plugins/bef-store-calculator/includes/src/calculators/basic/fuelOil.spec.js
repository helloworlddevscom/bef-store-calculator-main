import annualFuelOil from './fuelOil';

describe('Annual Fuel Oil Usage (gallons)', () => {
  it('calculates mT from annual fuel oil usage in gallons', () => {
    const input = 1500;
    const result = 15.31148046;
    expect(annualFuelOil(input)).toEqual(result);
  });
});