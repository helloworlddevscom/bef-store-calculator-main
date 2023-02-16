/**
 * Calculation for annual electricity and REC's
* */

/**
 *
 * @param inputVal
 * @param calculation
 * @param calculationREC
 * @param hiddenAnnualTotal
 * @param hiddenRECTotal
 * @param hiddenGridCode
 * @param totalCalc
 * @param forms
 * @param formId
 */
export function annualElectricityCalc({
  inputVal, calculation, calculationREC, hiddenAnnualTotal, hiddenRECTotal,
  hiddenGridCode, totalCalc, forms, formId,
}) {
  const result = calculation(parseFloat(inputVal.replace(/,/g, '')), hiddenGridCode);
  hiddenAnnualTotal.value = Number(result).toFixed(2);

  // REC calculation
  const rec = calculationREC(parseFloat(inputVal.replace(/,/g, '')));
  hiddenRECTotal.value = Number(rec).toFixed(2);

  // call function to update total with result
  if (formId === forms.businessFormId) {
    totalCalc.scope2Calc();
  }
  if (formId === forms.householdFormId) {
    totalCalc.scope1Calc();
  }
  totalCalc.emissionTotalCalc();
}
