import { forms } from '../calculators/constants';
import { chunkArray } from './functions/chunkArray';

/// Diet Select radio listener
/// Delegated event listener.   Bubble up tracking of event.target
export function confirmationRadioListener({
  className, hiddenSelect, hiddenTotal, totalCalc,
}) {
  const selectedElement = document.getElementsByClassName(className)[0];

  function deleteEntry() {
    totalCalc.scope5Calc();
    totalCalc.emissionTotalCalc();
  }

  selectedElement.addEventListener(
    'click',
    (event) => {
      const el = event.target.parentElement.classList.value;
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

/// Diet calculation
function selectResult({
  el, selectElementResult, totalCalc, calculation, formId,
}) {
  const hidden = document.getElementById(selectElementResult);

  let totalEmission = 0;
  //  DIET TYPE CALCULATIONS
  if (formId === forms.householdFormId) {
    const elArray = chunkArray(Array.from(el), 4);

    elArray.forEach((val, index) => {
      // building type
      const dietType = val[0].value;
      const mealsPerDay = parseFloat(val[1].value.replace(/,/g, '')) || '0';
      const NumPeople = parseFloat(val[2].value.replace(/,/g, '')) || '0';
      // Add JS subResult calculation to element
      const dataObj = { dietType, mealsPerDay, NumPeople };
      const subResult = calculation(dataObj);
      totalEmission += subResult;
      if (subResult > 0) {
        const elResult = val[3];
        elResult.readOnly = true;
        elResult.value = Number(subResult).toFixed(2);
      }
      return totalEmission;
    });
    // // call function to update total with result
    hidden.value = Number(totalEmission).toFixed(2);

    totalCalc.scope5Calc();
    totalCalc.emissionTotalCalc();
  }
}

/**
 *
 * @param className
 * @param elementName
 * @param selectElementResult
 * @param totalCalc
 * @param calculation
 * @param formId
 */
export function selectListener({
  className, elementName, selectElementResult, totalCalc, calculation, formId,
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
        el, selectElementResult, totalCalc, calculation, formId,
      });
    },
  );
}

/// Diet Remove row table listener
/// Delegated event listener.   Bubble up tracking of event.target
export function rowListener({
  className, elementName, selectElementResult, totalCalc, calculation, formId,
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
          el, selectElementResult, totalCalc, calculation, formId,
        });
      }
      // event.target.parentElement.parentElement.parentElement.children[1].firstChild.value
      // There are 2 elements available.
      // First is the selection, Second is the Value
      // WHen the user enters the value, send to getResult.
    },
  );
}
