import annualRentalCar from './rentalCar';

describe('Annual rental car Usage', () => {
  it('calculates mT from annual rental car usage', () => {
    const input = 5;
    const result = 0.196;
    expect(annualRentalCar(input)).toEqual(result);
  });
});