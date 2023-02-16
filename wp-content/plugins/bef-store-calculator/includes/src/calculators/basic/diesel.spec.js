import annualDiesel from './diesel';

describe('Annual Diesel Usage (gallons)', () => {
  it('calculates mT from annual diesel usage in gallons', () => {
    const input = 5000;
    const result = 51.0382682;
    expect(annualDiesel(input)).toEqual(result);
  });
});