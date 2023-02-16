/**
 *
 * @param result number
 * @returns {string}
 */
export default function totalEmissionFormat(result) {
  const value = result.toFixed(2);
  const formattedResult = `${value} mT`;
  return formattedResult;
}
