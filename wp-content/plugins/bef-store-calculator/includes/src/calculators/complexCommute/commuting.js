import { conversionFactor, vehicleEmission } from '../constants';

/**
 *
 * @param employees
 * @param miles
 * @returns {*}
 */
export default function commuting(employees, miles) {
  const defaultEmployees = {
    car: 0,
    hybrid: 0,
    electricVehicle: 0,
    trainRailSubway: 0,
    bus: 0,
    taxi: 0,
    ferry: 0,
  };
  const defaultMiles = {
    car: 0,
    hybrid: 0,
    electricVehicle: 0,
    trainRailSubway: 0,
    bus: 0,
    taxi: 0,
    ferry: 0,
  };

  const employeeNum = {};
  Object.keys(employees).forEach((key) => {
    if (Number.isNaN(employees[key])) {
      employeeNum[key] = defaultEmployees[key];
    } else {
      employeeNum[key] = employees[key];
    }
    return employeeNum;
  });

  const aveCommute = {};
  Object.keys(miles).forEach((key) => {
    if (Number.isNaN(miles[key])) {
      aveCommute[key] = defaultMiles[key];
    } else {
      aveCommute[key] = miles[key];
    }
    return aveCommute;
  });

  const carKey = 'car_gasoline';
  const c1 = employeeNum.car * aveCommute.car * 2 * 250;
  const c2 = vehicleEmission[carKey].carbondioxide_kg_mile;
  const c3 = vehicleEmission[carKey].methane_g_mile;
  const c4 = vehicleEmission[carKey].nitriousoxide_g_mile;
  const c5 = (c1 * c2) / conversionFactor.kg_mt.value;
  const c6 = c1 * c3;
  const c7 = c1 * c4;
  // c8 = MT Total Emission for Car
  const c8 = c5 + (c6 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c7 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
    / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;

  const c9 = employeeNum.hybrid * aveCommute.hybrid * 2 * 250;
  // c10 = Total Emission for Hybrid
  const c10 = (c9 * 0.191) / 1000;

  const c11 = employeeNum.electricVehicle * aveCommute.electricVehicle * 2 * 250;
  // c12 = Total Emissions for Electric Vehicle
  const c12 = (c11 * 0.123) / 1000;

  const c13 = employeeNum.trainRailSubway * aveCommute.trainRailSubway * 2 * 250;
  const c14 = vehicleEmission.train.carbondioxide_kg_mile;
  const c15 = vehicleEmission.train.methane_g_mile;
  const c16 = vehicleEmission.train.nitriousoxide_g_mile;
  const c17 = (c13 * c14) / conversionFactor.kg_mt.value;
  const c18 = c13 * c15;
  const c19 = c13 * c16;
  // c20 = Total Emissions for Rail
  const c20 = c17 + (c18 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c19 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
    / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;

  const c21 = employeeNum.bus * aveCommute.bus * 2 * 250;
  const c22 = vehicleEmission.bus.carbondioxide_kg_mile;
  const c23 = vehicleEmission.bus.methane_g_mile;
  const c24 = vehicleEmission.bus.nitriousoxide_g_mile;
  const c25 = (c21 * c22) / conversionFactor.kg_mt.value;
  const c26 = c21 * c23;
  const c27 = c21 * c24;
  // c28 = Total Emmisions for Bus
  const c28 = c25 + (c26 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c27 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
  / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;

  const c29 = employeeNum.taxi * aveCommute.taxi * 2 * 250;
  const c30 = vehicleEmission.taxi.carbondioxide_kg_mile;
  const c31 = vehicleEmission.taxi.methane_g_mile;
  const c32 = vehicleEmission.taxi.nitriousoxide_g_mile;
  const c33 = (c29 * c30) / conversionFactor.kg_mt.value;
  const c34 = c29 * c31;
  const c35 = c29 * c32;
  // c36 = Total Emission for Taxi
  const c36 = c33 + (c34 * conversionFactor.mt_carbon_dioxide_mt_methane.value + c35 * conversionFactor.mt_carbon_dioxide_mt_nitrious_dioxode.value)
  / conversionFactor.g_kg.value / conversionFactor.kg_mt.value;

  const c37 = employeeNum.ferry * aveCommute.ferry * 2 * 250;
  const c38 = vehicleEmission.ferry.carbondioxide_kg_km / conversionFactor.mile_km.value;
  // c39 = Total Emission for Ferry
  const c39 = (c37 * c38) / conversionFactor.kg_mt.value;

  const emission = c8 + c10 + c12 + c20 + c28 + c36 + c39;

  // console.log(
  //   {
  //     c1,
  //     c2,
  //     c3,
  //     c4,
  //     c5,
  //     c6,
  //     c7,
  //     c8,
  //     c9,
  //     c10,
  //     c11,
  //     c12,
  //     c13,
  //     c14,
  //     c15,
  //     c16,
  //     c17,
  //     c18,
  //     c19,
  //     c20,
  //     c21,
  //     c22,
  //     c23,
  //     c24,
  //     c25,
  //     c26,
  //     c27,
  //     c28,
  //     c29,
  //     c30,
  //     c31,
  //     c32,
  //     c33,
  //     c34,
  //     c35,
  //     c36,
  //     c37,
  //     c38,
  //     c39,
  //     emission,
  //   },
  return emission;
}
