import { forms } from '../calculators/constants';

import { annualElectricityCalc } from './electricityFunctions/annualElectricityCalc';
import { selectElectricityCalc } from './electricityFunctions/selectElectricityCalc';

/**
 *
 * @param inputID
 * @param hiddenAnnual
 * @param hiddenGRID
 * @param hiddenREC
 * @param totalCalc
 * @param calculation
 * @param calculationREC
 * @param formId
 */
export function annualListener({
  inputID, hiddenAnnual, hiddenGRID, hiddenREC, totalCalc, calculation, calculationREC, formId,
}) {
  const input = document.getElementById(inputID);

  // hidden total results for electricity entry
  const hiddenAnnualTotal = document.getElementById(hiddenAnnual);
  const hiddenGridCode = document.getElementById(hiddenGRID);
  const hiddenRECTotal = document.getElementById(hiddenREC);

  const eGRID = hiddenGridCode.value;

  input.addEventListener(
    'change',
    () => {
      const inputVal = input.value || '0';

      annualElectricityCalc({
        formId,
        forms,
        inputVal,
        hiddenAnnualTotal,
        hiddenRECTotal,
        hiddenGridCode: eGRID,
        calculation,
        calculationREC,
        totalCalc,
      });
    },
    false,
  );
}

/// Electricity Select radio listener
/// Delegated event listener.   Bubble up tracking of event.target
export function confirmationRadioListener({
  className, hiddenAnnual, hiddenSelect, hiddenTotal, totalCalc,
}) {
  const selectedElement = document.getElementsByClassName(className)[0];

  function deleteEntry() {
    totalCalc.scope1Calc();
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

/// Electricity Select calculation
function selectResult({
  el, selectElementResult, hiddenGRID, hiddenREC, totalCalc, calculation, calculationREC, formId,
}) {
  const hidden = document.getElementById(selectElementResult);
  const hGRID = document.getElementById(hiddenGRID);
  const hREC = document.getElementById(hiddenREC);

  const eGRID = hGRID.value;

  selectElectricityCalc({
    formId,
    forms,
    el,
    calculation,
    calculationREC,
    eGRID,
    hidden,
    hREC,
    totalCalc,
  });
}
/**
 *
 * Electricity Select table listener
 * Delegated event listener.   Bubble up tracking of event.target
 *
 * @param className
 * @param elementName
 * @param selectElementResult
 */
export function selectListener({
  className, elementName, selectElementResult,
  totalCalc, calculation, hiddenGRID, hiddenREC, calculationREC, formId,
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
        el, selectElementResult, totalCalc, calculation, hiddenGRID, hiddenREC, calculationREC, formId,
      });
    },
  );
}

/// Electricity table listener.   If remove/minus, trigger delete operation (-)
/// Delegated event listener.   Bubble up tracking of event.target
export function rowListener({
  className, elementName, selectElementResult, totalCalc,
  calculation, hiddenGRID, hiddenREC, calculationREC, formId,
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
          el, selectElementResult, hiddenGRID, hiddenREC, totalCalc, calculation, calculationREC, formId,
        });
      }
      // event.target.parentElement.parentElement.parentElement.children[1].firstChild.value
      // There are 2 elements available.
      // First is the building selection, Second is the Value
      // WHen the user enters the value, send to getResult.
    },
  );
}
