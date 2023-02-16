import annualPropane from './propane';

describe('Annual Propane Usage (gallons)', () => {
  it('calculates mT from annual propane usage in gallons', () => {
    const input = 20000;
    const result = 116.06298378893;
    expect(annualPropane(input)).toEqual(result);
  });
});