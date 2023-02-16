import offsiteServer from './offsiteServer';

describe('Annual number of offsiteServers', () => {
  it('calculates mT from number of offsite Servers', () => {
    const input = 5;
    const result = 26.39231;
    expect(offsiteServer(input)).toEqual(result);
  });
});