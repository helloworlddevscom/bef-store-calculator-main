import dietType from './dietType';

describe('Total personal diet calculation', () => {
  it('calculates mT from all household diet choices', () => {
    const selection = {
      dietType: 'meat_heavy',
      mealsPerDay: 3,
      NumPeople: 1,
    };
    const result = 2.624373808112583;
    expect(dietType(selection)).toEqual(result);
  });
});
