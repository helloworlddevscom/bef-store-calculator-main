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
      car: 10000,
      hybrid: 10000,
      electricVehicle: 10000,
      pickupTruckVan: 10000,
      deliveryTruck: 10000,
      semiBigRig: 10000,
    };
    const fuel = {
      car: 'gasoline',
      hybrid: '-',
      electricVehicle: '-',
      pickupTruckVan: 'gasoline',
      deliveryTruck: 'gasoline',
      semiBigRig: 'gasoline',
    };
    const result = 41.44147612326928;
    expect(ownedVehicleFleet(mpg, fuel, miles)).toEqual(result);
  });
});
