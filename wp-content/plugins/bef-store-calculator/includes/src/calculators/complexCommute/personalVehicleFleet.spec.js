import personalVehicleFleet from './personalVehicleFleet';

describe('Total personal vehicle fleet calculation', () => {
  it('calculates mT from all household vehicles', () => {
    const mpg = {
      car: 20,
      hybrid: 1,
      electricVehicle: 1,
      pickupTruckVan: 16.4,
    };
    const miles = {
      car: 40000,
      hybrid: 40000,
      electricVehicle: 40000,
      pickupTruckVan: 40000,
    };
    const result = 52.0229268292683;
    expect(personalVehicleFleet(mpg, miles)).toEqual(result);
  });
});
