import { forms } from '../calculators/constants';

/**
 *
 * @param calculation
 * @param totalCalc
 * @param hiddenTOTAL
 * @param formId
 */
function getFlightResult({
  calculation, totalCalc, hiddenTOTAL, formId,
}) {
  // Build array of all the input elements
  const flightTableClasses = ['.flightNum', '.RFIvalues'];

  const dataObj = [];
  flightTableClasses.forEach((arrayClass) => {
    let resultObj;
    const arrayElements = Array.from(document.querySelectorAll(arrayClass));
    if (arrayClass === '.flightNum') {
      resultObj = Object.fromEntries(
        arrayElements.map(
          (entry) => [entry.classList[1], parseFloat(entry.value.replace(/,/g, ''))],
        ),
      );
    } else {
      resultObj = Object.fromEntries(
        arrayElements.map(
          (entry) => [entry.classList[1], entry.value],
        ),
      );
    }
    dataObj.push(resultObj);
  });
  const result = calculation(...dataObj);

  // hidden total results for ownedVehicle Fleet
  const hidden = document.getElementById(hiddenTOTAL);
  hidden.value = Number(result).toFixed(2);

  // call function to update total with result
  if (formId === forms.businessFormId) {
    totalCalc.scope3Calc();
  }
  if (formId === forms.householdFormId) {
    totalCalc.scope4Calc();
  }
  totalCalc.emissionTotalCalc();
}

/// Flight Table Listener
/// Delegated event listener.   Bubble up tracking of event.target
export function flightTableListener({
  tableClassName, calculation, totalCalc, hiddenTOTAL, formId,
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
      getFlightResult({
        calculation, totalCalc, hiddenTOTAL, formId,
      });
    },
  );
}
