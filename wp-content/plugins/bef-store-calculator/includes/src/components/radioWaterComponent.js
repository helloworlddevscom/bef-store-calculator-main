import { chunkArray } from './functions/chunkArray';

/**
 *
 * @param inputID
 * @param hiddenAnnual
 * @param hiddenREC
 */
export function annualListener({
  inputID, hiddenWRC, calculationWRC, totalCalc,
}) {
  const input = document.getElementById(inputID);

  const hWRC = document.getElementById(hiddenWRC);

  input.addEventListener(
    'change',
    () => {
      const inputVal = input.value || '0';
      const rec = calculationWRC(parseFloat(inputVal.replace(/,/g, '')));
      hWRC.value = Number(rec).toFixed(2);

      // // call function to update total with result
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

///  Water radio listener
/// Delegated event listener.   Bubble up tracking of event.target
export function confirmationRadioListener({
  className, hiddenAnnual, hiddenSelect, hiddenTotal, totalCalc,
}) {
  const selectedElement = document.getElementsByClassName(className)[0];

  function deleteEntry() {
    totalCalc.emissionTotalCalc();
  }

  selectedElement.addEventListener(
    'click',
    (event) => {
      const el = event.target.parentElement.classList.value;
      const hiddenANN = document.getElementById(hiddenAnnual);
      const hiddenSEL = document.getElementById(hiddenSelect);
      const hiddenTOTAL = document.getElementById(hiddenTotal);
      const inputSelection = event.target.parentElement.parentElement.id.slice(5);

      if (hiddenTOTAL.value !== '0') {
        if (el.includes(`${inputSelection}_0`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenSEL.value = '0';
            hiddenTOTAL.value = '0';
            deleteEntry();
          } else {
            event.preventDefault();
          }
        }
        if (el.includes(`${inputSelection}_1`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenANN.value = '0';
            hiddenTOTAL.value = '0';
            deleteEntry();
          } else {
            event.preventDefault();
          }
        }
        if (el.includes(`${inputSelection}_2`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenANN.value = '0';
            hiddenSEL.value = '0';
            hiddenTOTAL.value = '0';
            deleteEntry();
          } else {
            event.preventDefault();
          }
        }
      }
      return true;
    },
  );
}

/// Water calculation
function selectResult({
  el, selectElementResult, totalCalc, calculation, calculationWRC,
}) {
  const hWRC = document.getElementById(selectElementResult);

  let totalGallons = 0;
  let totalWRC = 0;

  const elArray = chunkArray(Array.from(el), 3);
  let buildingType;
  let sqft;

  elArray.forEach((val, index) => {
    // building type
    buildingType = val[0].value;
    sqft = val[1].value || '0';
    // Add JS subResult calculation to element
    const subResult = calculation(parseFloat(sqft.replace(/,/g, '')), buildingType);
    totalGallons += subResult;
    if (subResult > 0) {
      const elResult = val[2];
      elResult.readOnly = true;
      elResult.value = Number(subResult).toFixed(2);
    }
    return totalGallons;
  });

  totalWRC = calculationWRC(totalGallons);

  hWRC.value = Number(totalWRC).toFixed(2);

  // // call function to update total with result
  totalCalc.emissionTotalCalc();
}

/**
 *
 * @param className
 * @param elementName
 * @param selectElementResult
 * @param totalCalc
 * @param calculation
 * @param calculationWRC
 */
export function selectListener({
  className, elementName, selectElementResult, totalCalc, calculation, calculationWRC,
}) {
  const selectedElement = document.getElementsByClassName(className)[0];
  const el = document.getElementsByName(elementName);

  selectedElement.addEventListener(
    'change',
    (event) => {
      const td = event.target.closest('td');

      if (!td) return;
      if (!selectedElement.contains(td)) return;

      // This contains the parent of the event.
      selectResult({
        el, selectElementResult, totalCalc, calculation, calculationWRC,
      });
    },
  );
}

/// Natural Gas Remove row table listener
/// Delegated event listener.   Bubble up tracking of event.target
export function rowListener({
  className, elementName, selectElementResult, totalCalc,
  calculation, calculationWRC,
}) {
  const selectedElement = document.getElementsByClassName(className)[0];
  const el = document.getElementsByName(elementName);

  selectedElement.addEventListener(
    'click',
    (event) => {
      // If the event doesn't have a title, not interested here
      if (event.target.title === '') return;

      const td = event.target.closest('td');
      if (!td) return;

      const condition = event.target.title.toLowerCase();
      if (condition.includes('remove')) {
        // This contains the parent <tr> of the event.
        selectResult({
          el, selectElementResult, totalCalc, calculation, calculationWRC,
        });
      }
      // event.target.parentElement.parentElement.parentElement.children[1].firstChild.value
      // There are 2 elements available.
      // First is the building selection, Second is the Value
      // WHen the user enters the value, send to getResult.
    },
  );
}
