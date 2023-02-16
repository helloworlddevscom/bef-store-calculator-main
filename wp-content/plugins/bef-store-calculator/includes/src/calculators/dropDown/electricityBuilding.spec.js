import electricityBuilding from './electricityBuilding';

describe('Annual electricity Usage (kWh)', () => {
  it('calculates mT from annual electricity usage in kWh', () => {
    const input = 5000;
    const buildingType = 'bar_pub_lounge';
    const eGrid = 'NWPP';
    const result = 38.11718532944453;
    expect(electricityBuilding(input, buildingType, eGrid)).toEqual(result);
  });
});