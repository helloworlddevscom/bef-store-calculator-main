import annualLodging from './lodging';

describe('Annual Lodging Usage', () => {
  it('calculates mT from annual lodging from total nights', () => {
    const input = 15;
    const result = 1.1532704345459495;
    expect(annualLodging(input)).toEqual(result);
  });
});