import waterWRC from './waterWRC';

describe('WRC calculation for water usage', () => {
  it('calculates WRC for annual water usage in gallons', () => {
    const input = 10000;
    const result = 10;
    expect(waterWRC(input)).toEqual(result);
  });
});