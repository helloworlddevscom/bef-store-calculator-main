/// Personal Vehicle Fleet
/**
 *
 * @param calculation
 * @param totalCalc
 * @param hiddenTOTAL
 */

function filterResults({ sourceObj, filterObj }) {
  const filtered = { ...sourceObj[1], ...filterObj };
  return [sourceObj[0], filtered];
}

function getPersonalVehicleResult({
  calculation, totalCalc, hiddenTOTAL, hiddenCarTotal, hiddenHybridTotal, hiddenElectricVehicleTotal, hiddenPickupTruckTotal,
}) {
  // Build array of all the input elements
  const personalTableClasses = ['.personalVehicleMPG', '.personalVehicleMiles'];

  const dataObj = [];
  personalTableClasses.forEach((arrayClass) => {
    const arrayElements = Array.from(document.querySelectorAll(arrayClass));
    const resultObj = Object.fromEntries(
      arrayElements.map(
        (entry) => [entry.classList[1], parseFloat(entry.value.replace(/,/g, ''))],
      ),
    );
    dataObj.push(resultObj);
  });

  const carFilter = { hybrid: 0, electricVehicle: 0, pickupTruckVan: 0 };
  const carInput = filterResults({ sourceObj: dataObj, filterObj: carFilter });
  const resultCar = calculation(...carInput);
  const hiddenCar = document.getElementById(hiddenCarTotal);
  hiddenCar.value = Number(resultCar).toFixed(2);

  const hybridFilter = { car: 0, electricVehicle: 0, pickupTruckVan: 0 };
  const hybridInput = filterResults({ sourceObj: dataObj, filterObj: hybridFilter });
  const resultHybrid = calculation(...hybridInput);
  const hiddenHybrid = document.getElementById(hiddenHybridTotal);
  hiddenHybrid.value = Number(resultHybrid).toFixed(2);

  const electricFilter = { car: 0, hybrid: 0, pickupTruckVan: 0 };
  const electricInput = filterResults({ sourceObj: dataObj, filterObj: electricFilter });
  const resultElectric = calculation(...electricInput);
  const hiddenElectric = document.getElementById(hiddenElectricVehicleTotal);
  hiddenElectric.value = Number(resultElectric).toFixed(2);

  const pickupFilter = { car: 0, hybrid: 0, electricVehicle: 0 };
  const pickupInput = filterResults({ sourceObj: dataObj, filterObj: pickupFilter });
  const resultPickup = calculation(...pickupInput);
  const hiddenPickup = document.getElementById(hiddenPickupTruckTotal);
  hiddenPickup.value = Number(resultPickup).toFixed(2);

  const result = calculation(...dataObj);
  // hidden total results for personalVehicle Fleet

  const hidden = document.getElementById(hiddenTOTAL);
  hidden.value = Number(result).toFixed(2);

  // call function to update total with result
  totalCalc.scope3Calc();
  totalCalc.emissionTotalCalc();
}

/// personal Vehicle Fleet
/// Delegated event listener.   Bubble up tracking of event.target
export function personalVehicleListener({
  tableClassName, calculation, totalCalc, hiddenTOTAL,
  hiddenCarTotal, hiddenHybridTotal, hiddenElectricVehicleTotal, hiddenPickupTruckTotal,
}) {
  const selectedElement = document.getElementsByClassName(tableClassName)[0];

  selectedElement.addEventListener(
    'change',
    (event) => {
      const td = event.target.closest('td');

      if (!td) return;
      if (!selectedElement.contains(td)) return;

      const assignedElement = document.getElementsByClassName(event.target.className)[0];
      assignedElement.setAttribute('value', event.target.value);
      getPersonalVehicleResult({
        calculation, totalCalc, hiddenTOTAL, hiddenCarTotal, hiddenHybridTotal, hiddenElectricVehicleTotal, hiddenPickupTruckTotal,
      });
    },
  );
}
