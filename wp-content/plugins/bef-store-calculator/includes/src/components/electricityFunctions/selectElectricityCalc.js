import { chunkArray } from '../functions/chunkArray';

/**
 *
 * @param formId
 * @param forms
 * @param el
 * @param calculation
 * @param calculationREC
 * @param eGRID
 * @param hidden
 * @param hREC
 * @param totalCalc
 */
export function selectElectricityCalc({
  formId,
  forms,
  el,
  calculation,
  calculationREC,
  eGRID,
  hidden,
  hREC,
  totalCalc,
}) {
  let totalEmission = 0;
  let totalREC = 0;

  //  BUSINESS TYPE CALCULATIONS
  if (formId === forms.businessFormId) {
    const elArray = chunkArray(Array.from(el), 3);
    let buildingType;
    let sqft;

    elArray.forEach((val, index) => {
      // building type
      buildingType = val[0].value;
      sqft = val[1].value || '0';
      // Add JS subResult calculation to element
      const subResult = calculation(parseFloat(sqft.replace(/,/g, '')), buildingType, eGRID);
      totalEmission += subResult;
      if (subResult > 0) {
        const elResult = val[2];
        elResult.readOnly = true;
        elResult.value = Number(subResult).toFixed(2);
      }
      return totalEmission;
    });

    elArray.forEach((val, index) => {
      // building type
      buildingType = val[0].value;
      sqft = val[1].value || '0';
      // Add JS subResult calculation to element
      totalREC += calculationREC(parseFloat(sqft.replace(/,/g, '')), buildingType);
      return totalREC;
    });

    hidden.value = Number(totalEmission).toFixed(2);
    hREC.value = Number(totalREC).toFixed(2);

    // // call function to update total with result

    totalCalc.scope2Calc();
    totalCalc.emissionTotalCalc();
  }
  if (formId === forms.householdFormId) {
    const elArray = chunkArray(Array.from(el), 2);
    let householdType;
    elArray.forEach((val, index) => {
      // building type
      householdType = val[0].value;
      // Add JS subResult calculation to element
      const subResult = calculation(householdType, eGRID);
      totalEmission += subResult;
      if (subResult > 0) {
        const elResult = val[1];
        elResult.readOnly = true;
        elResult.value = Number(subResult).toFixed(2);
      }
      return totalEmission;
    });

    elArray.forEach((val, index) => {
      // building type
      householdType = val[0].value;
      // Add JS subResult calculation to element
      totalREC += calculationREC(householdType);
      return totalREC;
    });

    hidden.value = Number(totalEmission).toFixed(2);
    hREC.value = Number(totalREC).toFixed(2);

    // // call function to update total with result
    totalCalc.scope1Calc();
    totalCalc.emissionTotalCalc();
  }
}
