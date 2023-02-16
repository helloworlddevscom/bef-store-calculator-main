// Summarize all the hidden fields for the total calculation
import { hiddenValues, fields } from './fieldmap';
import { totalEmissionFormat } from '../calculators/utility';

export function emissionTotalCalc() {
  const totalResult = document.getElementById('report-menu__emissions--result');
  const totalHidden = document.getElementById(hiddenValues.flight_total.id);

  // Flight Totals
  const AnnualFlightTotal = document.getElementById(hiddenValues.annual_flight_total.id).value;
  const AnnualFlightTotalParsed = parseFloat(AnnualFlightTotal.replace(/,/g, ''));

  const SelectFlightTotal = document.getElementById(hiddenValues.select_flight_total.id).value;
  const SelectFlightTotalParsed = parseFloat(SelectFlightTotal.replace(/,/g, ''));

  const TableFlightTotal = document.getElementById(hiddenValues.table_flight_total.id).value;
  const TableFlightTotalParsed = parseFloat(TableFlightTotal.replace(/,/g, ''));

  const FlightTotalParsed = AnnualFlightTotalParsed + SelectFlightTotalParsed + TableFlightTotalParsed;
  const FlightTotal = document.getElementById(hiddenValues.flight_total.id);
  FlightTotal.value = FlightTotalParsed;

  const totalEntry = [
    FlightTotalParsed,
  ];

  // Summarize all entries and apply to hidden value;
  const result = totalEntry.reduce((a, b) => a + b, 0);
  totalHidden.value = Number(result);

  // push to display result
  totalResult.innerText = totalEmissionFormat(result);
}
