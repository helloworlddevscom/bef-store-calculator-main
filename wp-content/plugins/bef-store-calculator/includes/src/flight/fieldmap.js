import { forms } from '../calculators/constants';

export const fields = {
  flight_input: {
    name: 'input_3',
    id: `input_${forms.flightFormId}_3`,
    desc: 'flight_input',
    unit: 'miles',
    label: 'flight_input',
  },
  flight_select_input: {
    name: 'input_4[]',
    id: `input_${forms.flightFormId}_4`,
    desc: 'flight_select_input',
    unit: 'miles',
    label: 'flight_select_input',
  },
};

export const hiddenValues = {
  form_total: {
    name: 'input_11',
    id: `input_${forms.flightFormId}_11`,
    desc: 'total emissions for flight calculator',
    units: 'mT',
    label: 'form_total',
  },
  annual_flight_total: {
    name: 'input_7',
    id: `input_${forms.flightFormId}_7`,
    desc: 'total emission for annual flights',
    units: 'miles',
    label: 'annual_flight_total',
  },
  select_flight_total: {
    name: 'input_8',
    id: `input_${forms.flightFormId}_8`,
    desc: 'total emission for select flights',
    units: 'miles',
    label: 'select_flight_total',
  },
  table_flight_total: {
    name: 'input_9',
    id: `input_${forms.flightFormId}_9`,
    desc: 'total emission for haul flights',
    units: 'miles',
    label: 'table_flight_total',
  },
  flight_total: {
    name: 'input_10',
    id: `input_${forms.flightFormId}_10`,
    desc: 'total emission for flight travel',
    units: 'miles',
    label: 'flight_total',
  },
};
