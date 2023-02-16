import offsiteREC from './offsiteREC';

describe('REC calculation from kWh', () => {
  it('calculates RECs for offsite server usage', () => {
    const input = 5;
    const result = 37.33;
    expect(offsiteREC(input)).toEqual(result);
  });
});