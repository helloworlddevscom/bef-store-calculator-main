import annualFlight from './flight';

describe('Annual Flight Usage (miles)', () => {
  it('calculates mT from annual flight mile number', () => {
    const input = 4860;
    const result = 0.82199321316;
    expect(annualFlight(input)).toEqual(result);
  });
});
