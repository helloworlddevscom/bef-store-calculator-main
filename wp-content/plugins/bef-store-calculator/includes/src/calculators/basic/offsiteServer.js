/**
 *
 * @param servers
 * @returns {number}
 */
export default function offsiteServer(servers = 0) {
  const c1 = servers * 7466;
  const emission = c1 * 0.000707;
  return emission;
}
