import commuting from './commuting';

describe('Total commuting calculation', () => {
  it('calculates mT from all business commutes', () => {
    const employees = {
      car: 20,
      hybrid: 35,
      electricVehicle: 60,
      trainRailSubway: 150,
      bus: 110,
      taxi: 5,
      ferry: 45,
    };
    const miles = {
      car: 5,
      hybrid: 3,
      electricVehicle: 10,
      trainRailSubway: 2.5,
      bus: 6,
      taxi: 7,
      ferry: 0.75,
    };
    const result = 139.97672519636708;
    expect(commuting(employees, miles)).toEqual(result);
  });
});