/**
 *
 * @param calculation
 * @param totalCalc
 * @param hiddenTOTAL
 */
function getCommuteResult({ calculation, totalCalc, hiddenTOTAL }) {
  // Build array of all the input elements
  const commutingClasses = ['.numEmployees', '.aveCommuteMiles'];

  const dataObj = [];
  commutingClasses.forEach((arrayClass) => {
    const arrayElements = Array.from(document.querySelectorAll(arrayClass));
    const resultObj = Object.fromEntries(
      arrayElements.map(
        (entry) => [entry.classList[1], parseFloat(entry.value.replace(/,/g, ''))],
      ),
    );
    dataObj.push(resultObj);
  });
  const result = calculation(...dataObj);

  // hidden total results for ownedVehicle Fleet
  const hidden = document.getElementById(hiddenTOTAL);
  hidden.value = Number(result).toFixed(2);

  // call function to update total with result
  totalCalc.scope3Calc();
  totalCalc.emissionTotalCalc();
}

/// Commuting table listener
/// Delegated event listener.   Bubble up tracking of event.target
export function commutingListener({
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
      getCommuteResult({ calculation, totalCalc, hiddenTOTAL });
    },
  );
}
