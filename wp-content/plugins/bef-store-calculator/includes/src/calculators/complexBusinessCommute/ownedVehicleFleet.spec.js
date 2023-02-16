import ownedVehicleFleet from './ownedVehicleFleet';

describe('Total owned vehicle fleet calculation', () => {
  it('calculates mT from all business vehicle fleets', () => {
    const mpg = {
      car: 20,
      hybrid: 1,
      electricVehicle: 1,
      pickupTruckVan: 16.4,
      deliveryTruck: 6.7,
      semiBigRig: 5.8,
    };
    const miles = {
      car: 40000,
      hybrid: 40000,
      electricVehicle: 40000,
      pickupTruckVan: 40000,
      deliveryTruck: 40000,
      semiBigRig: 40000,
    };
    const result = 182.61994175464145;
    expect(ownedVehicleFleet(mpg, miles)).toEqual(result);
  });
});