/**
 *
 * @param annualUsage
 * @returns number
 */
export default function annualLodging(annualUsage) {
  const emission = (annualUsage * 169.5) / 2204.6;
  return emission;
}
