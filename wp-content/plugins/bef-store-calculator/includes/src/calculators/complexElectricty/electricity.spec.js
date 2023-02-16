import annualElectricity from './electricity';

describe('Annual electricity Usage (kWh)', () => {
  it('calculates mT from annual electricity usage in kWh', () => {
    const input = 100000;
    const eGrid = 'NWPP';
    const result = 28.986452722011048;
    expect(annualElectricity(input, eGrid)).toEqual(result);
  });
});