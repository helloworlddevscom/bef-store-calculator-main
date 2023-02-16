import naturalGas from './naturalGas';

describe('Annual Natural Gas Usage (therms)', () => {
  it('calculates mT from annual gas usage in therms', () => {
    const input = 14345;
    const result = 74.73573106948486;
    expect(naturalGas(input)).toEqual(result);
  });
});