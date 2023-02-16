import waterREC from './waterREC';

describe('REC calculation for water usage', () => {
  it('calculates RECs for annual water usage in gallons', () => {
    const input = 10000;
    const result = 10;
    expect(waterREC(input)).toEqual(result);
  });
});