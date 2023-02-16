/**
 *
 * @param inputID
 * @param hiddenWRC
 * @param calculationWRC
 * @param totalCalc
 * @param hiddenAnnualTotal
 */
export function annualListener({
  inputID, hiddenWRC, calculationWRC, totalCalc, hiddenAnnualTotal,
}) {
  const input = document.getElementById(inputID);

  const hWRC = document.getElementById(hiddenWRC);
  const hiddenAnnual = document.getElementById(hiddenAnnualTotal);

  input.addEventListener(
    'change',
    () => {
      const inputVal = input.value || '0';
      // Raw Gallon count for summary
      const total = parseFloat(inputVal.replace(/,/g, ''));
      hiddenAnnual.value = Number(total).toFixed(2);

      // WRC calculation
      const wrc = calculationWRC(parseFloat(inputVal.replace(/,/g, '')));
      hWRC.value = Number(wrc).toFixed(2);

      // // call function to update total with result
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

/**
 *
 * @param inputID
 * @param hiddenAnnual
 * @param hiddenREC
 */
export function householdAveListener({
  inputID, hiddenWRC, calculation, totalCalc, resultWRC, hiddenSelectTotal,
}) {
  const input = document.getElementById(inputID);

  const hWRC = document.getElementById(hiddenWRC);

  const hiddenTotal = document.getElementById(hiddenSelectTotal);

  input.addEventListener(
    'change',
    () => {
      const inputVal = input.value || '0';
      const total = calculation(parseFloat(inputVal.replace(/,/g, '')));
      hiddenTotal.value = Number(total).toFixed(2);

      const wrc = resultWRC(total);
      hWRC.value = Number(wrc).toFixed(2);

      // // call function to update total with result
      totalCalc.emissionTotalCalc();
    },
    false,
  );
}

/// Natural Gas Select radio listener
/// Delegated event listener.   Bubble up tracking of event.target
export function confirmationRadioListener({
  className, hiddenAnnual, hiddenSelect, hiddenTotal, totalCalc, annualWRCElementTotal, selectWRCElementTotal, radioWRCTotal
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
      const hiddenWRCANN = document.getElementById(annualWRCElementTotal);
      const hiddenWRCSEL = document.getElementById(selectWRCElementTotal);
      const hiddenWRCTOTAL = document.getElementById(radioWRCTotal);
      const inputSelection = event.target.parentElement.parentElement.id.slice(5);

      if (hiddenTOTAL.value !== '0') {
        if (el.includes(`${inputSelection}_0`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenSEL.value = '0';
            hiddenWRCSEL.value = '0';
            hiddenTOTAL.value = '0';
            hiddenWRCTOTAL.value = '0';
            deleteEntry();
          } else {
            event.preventDefault();
          }
        }
        if (el.includes(`${inputSelection}_1`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenANN.value = '0';
            hiddenWRCANN.value = '0';
            hiddenTOTAL.value = '0';
            hiddenWRCTOTAL.value = '0';
            deleteEntry();
          } else {
            event.preventDefault();
          }
        }
        if (el.includes(`${inputSelection}_2`)) {
          if (confirm('Changing your selection will delete the data currently in this field, are you sure?')) {
            hiddenANN.value = '0';
            hiddenWRCANN.value = '0';
            hiddenSEL.value = '0';
            hiddenWRCSEL.value = '0';
            hiddenTOTAL.value = '0';
            hiddenWRCTOTAL.value = '0';
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
