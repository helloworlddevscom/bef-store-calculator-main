/**
 *
 * @param inputID
 * @param hiddenID
 */
import { flightDistanceCalculation } from '../calculators/utility';
import { forms } from '../calculators/constants';
import { chunkArray } from './functions/chunkArray';

export function annualListener({
  formId, inputID, hiddenID, totalCalc, calculation,
}) {
  const input = document.getElementById(inputID);

  // hidden total results for Flight Calculator entry
  const hidden = document.getElementById(hiddenID);

  input.addEventListener(
    'change',
    () => {
      const inputVal = input.value || '0';
      //  Add that result to the hidden value for this entry
      const result = calculation(parseFloat(inputVal.replace(/,/g, '')));
      hidden.value = Number(result).toFixed(2);
      // call function to update total with result
      if (formId === forms.businessFormId) {
        totalCalc.scope3Calc();
      }
      if (formId === forms.householdFormId) {
        totalCalc.scope4Calc();
      }
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

/// Flight Calculator Select radio listener
/// Delegated event listener.   Bubble up tracking of event.target
export function confirmationRadioListener({
  className, hiddenAnnual, hiddenSelect, hiddenTable, hiddenTotal, totalCalc, formId
}) {
  const selectedElement = document.getElementsByClassName(className)[0];

  function deleteEntry() {
    // call function to delete amounts
    if (formId === forms.businessFormId) {
      totalCalc.scope3Calc();
    }
    if (formId === forms.householdFormId) {
      totalCalc.scope4Calc();
    }
    totalCalc.emissionTotalCalc();
  }

  selectedElement.addEventListener(
    'click',
    (event) => {
      const el = event.target.parentElement.classList.value;
      const hiddenANN = document.getElementById(hiddenAnnual);
      const hiddenSEL = document.getElementById(hiddenSelect);
      const hiddenTAB = document.getElementById(hiddenTable);
      const hiddenTOTAL = document.getElementById(hiddenTotal);
      const inputSelection = event.target.parentElement.parentElement.id.slice(5);

      if (hiddenTOTAL.value !== '0' ) {
        if (el.includes(`${inputSelection}_0`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenSEL.value = '0';
            hiddenTAB.value = '0';
            hiddenTOTAL.value = '0';
            deleteEntry();
          } else {
            event.preventDefault();
          }
        }
        if (el.includes(`${inputSelection}_1`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenANN.value = '0';
            hiddenTAB.value = '0';
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

/// Flight Calculator calculation
function selectResult({
  formId, arr, selectElementResult, totalCalc, calculation,
}) {
  const hidden = document.getElementById(selectElementResult);

  let totalEmission = 0;
  // arr is an array (from a node list) for each submission.   It contains the airports, the multiplier.
  function makeRequest(id) {
    return flightDistanceCalculation(id[0].value, id[1].value, id);
  }

  function makeRequests(results) {
    const requests = [];
    for (let i = 0; i < results.length; i++) {
      requests.push(makeRequest(results[i]));
    }
    return Promise.all(requests);
  }

  makeRequests(arr).then((dataArray) => {
    dataArray.forEach((response) => {
      let total;
      if (response.miles !== 0) {
        total = Number(response.el[2].value) * Number(response.el[3].value) * Math.round(response.miles);
        response.el[4].value = total;
      } else {
        total = response.miles;
        response.el[4].value = '';
      }
      totalEmission += calculation(total);
    });
    return totalEmission;
  }).then((emission) => {
    hidden.value = Number(emission).toFixed(2);
    // // call function to update total with result
    if (formId === forms.businessFormId) {
      totalCalc.scope3Calc();
    }
    if (formId === forms.householdFormId) {
      totalCalc.scope4Calc();
    }
    totalCalc.emissionTotalCalc();
  });
}

/**
 *
 * Flight Calculator Select table listener
 * Delegated event listener.   Bubble up tracking of event.target
 *
 * @param className
 * @param elementName
 * @param selectElementResult
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

      const arr = chunkArray(Array.from(el), 5);

      let inc = 0;
      arr.forEach((val) => {
        // Mark as ready for calculation if all elements are filled out
        if ((val[0].value !== '' && val[1].value !== '' && val[2].value !== '' && val[3].value !== '')
         || (val[0].value === '' && val[1].value === '' && val[2].value === '' && val[3].value === '')) {
          inc += 1;
        }
      });

      // Only send AJAX function if all entries are filled out
      if (inc === arr.length) {
        selectResult({
          formId, arr, event, selectElementResult, totalCalc, calculation,
        });
      }
    },
  );
}

/// Flight Calculator Remove row table listener
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
        const arr = chunkArray(Array.from(el), 5);
        let inc = 0;
        arr.forEach((val) => {
          // Mark as ready for calculation if all elements are filled out
          if ((val[0].value !== '' && val[1].value !== '' && val[2].value !== '' && val[3].value !== '')
            || (val[0].value === '' && val[1].value === '' && val[2].value === '' && val[3].value === '')) {
            inc += 1;
          }
        });
        // Only send to googleAPI if all entries are filled out
        if (inc === arr.length) {
          selectResult({
            formId, arr, event, selectElementResult, totalCalc, calculation,
          });
        }
      }
    },
  );
}
