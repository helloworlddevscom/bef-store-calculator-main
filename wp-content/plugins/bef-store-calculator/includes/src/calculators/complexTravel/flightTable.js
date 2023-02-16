import { conversionFactor, vehicleEmission } from '../constants';

/**
 *
 * @param flights
 * @param RFI
 * @returns {*}
 */
export default function flightTable(flights, RFI) {
  const defaultFlights = {
    shortHaul: 0,
    mediumHaul: 0,
    longHaul: 0,
  };
  const defaultRFI = {
    shortHaul: 'NO',
    mediumHaul: 'NO',
    longHaul: 'NO',
  };

  const flightNum = {};
  Object.keys(flights).forEach((key) => {
    if (Number.isNaN(flights[key])) {
      flightNum[key] = defaultFlights[key];
    } else {
      flightNum[key] = flights[key];
    }
    return flightNum;
  });

  const RFIvalues = {};
  Object.keys(RFI).forEach((key) => {
    if (Number.isNaN(RFI[key])) {
      RFIvalues[key] = defaultRFI[key];
    } else {
      RFIvalues[key] = RFI[key];
    }
    return RFIvalues;
  });

  const c1 = flightNum.shortHaul * 462 * 2 * conversionFactor.mile_km.value;
  const c2 = flightNum.mediumHaul * 1108 * 2 * conversionFactor.mile_km.value;
  const c3 = flightNum.longHaul * 6482 * 2 * conversionFactor.mile_km.value;
  const c4 = vehicleEmission.air_domestic.carbondioxide_kg_km / conversionFactor.mile_km.value;
  const c5 = vehicleEmission.air_short_haul.carbondioxide_kg_km / conversionFactor.mile_km.value;
  const c6 = vehicleEmission.air_long_haul.carbondioxide_kg_km / conversionFactor.mile_km.value;

  // Short Haul Emissions
  let shortHaulTotal;
  if (RFIvalues.shortHaul === 'YES') {
    shortHaulTotal = (c1 * c4 * conversionFactor.rfi.value) / conversionFactor.kg_mt.value;
  } else {
    shortHaulTotal = (c1 * c4) / conversionFactor.kg_mt.value;
  }

  // Medium Haul Emissions
  let mediumHaulTotal;
  if (RFIvalues.mediumHaul === 'YES') {
    mediumHaulTotal = (c2 * c5 * conversionFactor.rfi.value) / conversionFactor.kg_mt.value;
  } else {
    mediumHaulTotal = (c2 * c5) / conversionFactor.kg_mt.value;
  }

  // Long Haul Emissions
  let longHaulTotal;
  if (RFIvalues.longHaul === 'YES') {
    longHaulTotal = (c3 * c6 * conversionFactor.rfi.value) / conversionFactor.kg_mt.value;
  } else {
    longHaulTotal = (c3 * c6) / conversionFactor.kg_mt.value;
  }
  return shortHaulTotal + mediumHaulTotal + longHaulTotal;
}
