/// Diet Calculation
/**
 *
 * @param calculation
 * @param totalCalc
 * @param hiddenTOTAL
 */
function getPersonalDietResult({ calculation, totalCalc, hiddenTOTAL }) {
  // Build array of all the input elements
  const personalTableClasses = ['.dietValue'];

  const dataObj = [];
  personalTableClasses.forEach((arrayClass) => {
    const arrayElements = Array.from(document.querySelectorAll(arrayClass));
    let result = {};
    const resultObj = Object.fromEntries(
      arrayElements.map(
        // If not a number (or a string), just pass along
        (entry) => {
          if (entry.classList[1] === 'dietType') {
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

  // hidden total results for personalDiet Fleet
  const hidden = document.getElementById(hiddenTOTAL);
  hidden.value = Number(result).toFixed(2);

  // call function to update total with result
  totalCalc.scope5Calc();
  totalCalc.emissionTotalCalc();
}

/// personal Diet Fleet
/// Delegated event listener.   Bubble up tracking of event.target
export function personalDietListener({
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
      getPersonalDietResult({ calculation, totalCalc, hiddenTOTAL });
    },
  );
}
