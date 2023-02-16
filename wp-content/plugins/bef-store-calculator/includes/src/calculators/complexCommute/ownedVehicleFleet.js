import { conversionFactor, vehicleEmission } from '../constants';

/**
 *
 * @param mpg
 * @param fuel
 * @param miles
 * @returns {number}
 */
export default function ownedVehicleFleet(mpg, fuel, miles) {
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
  const defaultFuelType = {
    car: 'gasoline',
    hybrid: '-',
    electricVehicle: '-',
    pickupTruckVan: 'gasoline',
    deliveryTruck: 'gasoline',
    semiBigRig: 'gasoline',
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

  const fuelType = {};
  Object.keys(fuel).forEach((key) => {
    if (Number.isNaN(fuel[key])) {
      fuelType[key] = defaultFuelType[key];
    } else {
      fuelType[key] = fuel[key];
    }
    return fuelType;
  });

  const carKey = `car_${fuelType.car}`;
  const c1 = ((mileage.car / vehiclesMPG.car) * vehicleEmission[carKey].carbondioxide_kg_mile) / conversionFactor.kg_mt.value;
  const c2 = (mileage.car * vehicleEmission[carKey].methane_g_mile);
  const c3 = (mileage.car * vehicleEmission[carKey].nitriousoxide_g_mile);
  // Total emission car
  const carResult = c1 + (c2 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c3 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
              / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;
  const hybridResult = (0.191 * mileage.hybrid) / 1000;
  const electricVehicleResult = (0.123 * mileage.electricVehicle) / 1000;

  const pickupTruckVanKey = `pickupTruckVan_${fuelType.pickupTruckVan}`;
  const c4 = ((mileage.pickupTruckVan / vehiclesMPG.pickupTruckVan) * vehicleEmission[pickupTruckVanKey].carbondioxide_kg_mile) / conversionFactor.kg_mt.value;
  const c5 = (mileage.pickupTruckVan * vehicleEmission[pickupTruckVanKey].methane_g_mile);
  const c6 = (mileage.pickupTruckVan * vehicleEmission[pickupTruckVanKey].nitriousoxide_g_mile);
  const pickupTruckVanResult = c4 + (c5 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c6 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
                                / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;
  const deliveryTruckKey = `deliveryTruck_${fuelType.deliveryTruck}`;
  const c7 = ((mileage.deliveryTruck / vehiclesMPG.deliveryTruck) * vehicleEmission[deliveryTruckKey].carbondioxide_kg_mile) / conversionFactor.kg_mt.value;
  const c8 = (mileage.deliveryTruck * vehicleEmission[deliveryTruckKey].methane_g_mile);
  const c9 = (mileage.deliveryTruck * vehicleEmission[deliveryTruckKey].nitriousoxide_g_mile);
  const deliveryTruckResult = c7 + (c8 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c9 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
                              / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;

  const semiBigRig = `semiBigRig_${fuelType.semiBigRig}`;
  const c10 = ((mileage.semiBigRig / vehiclesMPG.semiBigRig) * vehicleEmission[semiBigRig].carbondioxide_kg_mile) / conversionFactor.kg_mt.value;
  const c11 = (mileage.semiBigRig * vehicleEmission[semiBigRig].methane_g_mile);
  const c12 = (mileage.semiBigRig * vehicleEmission[semiBigRig].nitriousoxide_g_mile);
  const semiBigRigResult = c10 + (c11 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c12 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
                            / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;
  const emission = carResult + hybridResult + electricVehicleResult + pickupTruckVanResult + deliveryTruckResult + semiBigRigResult;
  return emission;
}
