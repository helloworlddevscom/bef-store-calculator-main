import flightTable from './flightTable';

describe('Total flight table calculation', () => {
  it('calculates mT from all flight table calcs', () => {
    const flights = {
      shortHaul: 10,
      mediumHaul: 5,
      longHaul: 1,
    };
    const RFI = {
      shortHaul: 'YES',
      mediumHaul: 'NO',
      longHaul: 'NO',
    };
    const result = 6.8199887200000004;
    expect(flightTable(flights, RFI)).toEqual(result);
  });
});
