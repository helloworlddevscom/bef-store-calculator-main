/**
 *
 * @param mpg
 * @param miles
 * @returns {number}
 */
export default function ownedVehicleFleet(mpg, miles) {
  const defaultMPG = {
    car: 20,
    pickupTruckVan: 16.4,
    deliveryTruck: 6.7,
    semiBigRig: 5.8,
  };
  const defaultMiles = {
    car: 0,
    hybrid: 0,
    electricVehicle: 0,
    pickupTruckVan: 0,
    deliveryTruck: 0,
    semiBigRig: 0,
  };
  // NOTE:  Setting Average MPG for electric hybrid and electric are non-existent.
  // So setting to unity.
  const defaultHybrid = {
    hybrid: 1,
    electricVehicle: 1,
  };

  const vehiclesMPG = { ...defaultMPG, ...mpg, ...defaultHybrid }; // right-most object overwrites
  // Only apply Defaults of 0 to mileage in NaN input... otherwise, use result
  //
  const mileage = {};
  Object.keys(miles).forEach((key) => {
    if (Number.isNaN(miles[key])) {
      mileage[key] = defaultMiles[key];
    } else {
      mileage[key] = miles[key];
    }
    return mileage;
  });

  // console.log({ vehiclesMPG, mileage });

  const carResult = (8.89 * 0.001 * mileage.car) / vehiclesMPG.car;
  const hybridResult = (0.191 * mileage.hybrid) / 1000;
  const electricVehicleResult = (0.123 * mileage.electricVehicle) / 1000;
  const pickupTruckVanResult = (8.89 * 0.001 * mileage.pickupTruckVan) / vehiclesMPG.pickupTruckVan;
  const deliveryTruckResult = (10.15 * 0.001 * mileage.deliveryTruck) / vehiclesMPG.deliveryTruck;
  const semiBigRigResult = (10.15 * 0.001 * mileage.semiBigRig) / vehiclesMPG.semiBigRig;
  const emission = carResult + hybridResult + electricVehicleResult + pickupTruckVanResult + deliveryTruckResult + semiBigRigResult;
  // console.log({carResult, hybridResult, electricVehicleResult, pickupTruckVanResult, deliveryTruckResult, semiBigRigResult});

  return emission;
}
