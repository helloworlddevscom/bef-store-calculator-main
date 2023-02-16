/// Owned Vehicle Fleet
/**
 *
 * @param calculation
 * @param totalCalc
 * @param hiddenTOTAL
 */
function getOwnedVehicleResult({ calculation, totalCalc, hiddenTOTAL }) {
  // Build array of all the input elements
  // NOTE:  These classes are unique and defined in
  // wp-content/plugins/bef-store-calculator/public/partials/class-bef-store-calculator-owned-vehicle.php
  const ownedTableClasses = ['.ownedVehicleMPG', '.ownedVehicleFuelType', '.ownedVehicleMiles'];

  const dataObj = [];
  ownedTableClasses.forEach((arrayClass) => {
    const arrayElements = Array.from(document.querySelectorAll(arrayClass));
    const resultObj = Object.fromEntries(
      arrayElements.map(
        (entry) => {
          let result;
          if (arrayClass === '.ownedVehicleFuelType') {
            result = [entry.classList[1], entry.value];
          } else {
            result = [entry.classList[1], parseFloat(entry.value.replace(/,/g, ''))];
          }
          return result;
        },
      ),
    );
    dataObj.push(resultObj);
  });
  const result = calculation(...dataObj);

  // hidden total results for ownedVehicle Fleet
  const hidden = document.getElementById(hiddenTOTAL);
  hidden.value = Number(result).toFixed(2);

  // call function to update total with result
  totalCalc.scope1Calc();
  totalCalc.emissionTotalCalc();
}

/// Owned Vehicle Fleet
/// Delegated event listener.   Bubble up tracking of event.target
export function ownedVehicleListener({
  tableClassName, calculation, totalCalc, hiddenTOTAL,
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
      getOwnedVehicleResult({ calculation, totalCalc, hiddenTOTAL });
    },
  );
}
