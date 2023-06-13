const klimaBreakpointA = 629;
const klimaBreakpointB = 1258;
const klimaBreakpointC = 2667;
const klimaBreakpointD = 4853;

export function getKlimaScoreRanking(klimaScore: number | null): string | null {
  if (!klimaScore) return null;

  if (klimaScore === 0) {
    return null;
  } else if (klimaScore < klimaBreakpointA) {
    return 'A';
  } else if (klimaScore < klimaBreakpointB) {
    return 'B';
  } else if (klimaScore < klimaBreakpointC) {
    return 'C';
  } else if (klimaScore < klimaBreakpointD) {
    return 'D';
  } else {
    return 'E';
  }
}

export function calculateKlimaPercent(emission: number) {
  const emissionAverage = 1258;
  const co2Percentage = emission * 100 / emissionAverage - 100;
  return Math.round(co2Percentage * 10) / 10;
}

export function calculateWaterPercent(water: number) {
  const waterAverage = 21;
  const waterPercentage = water * 100 / waterAverage - 100;
  return Math.round(waterPercentage * 10) / 10;
}

export function calculateVitaPercent(riskpoints: number) {
  const riskpointsAverage = 400;
  const vitaPercentage = riskpoints * 100 / riskpointsAverage - 100;
  return Math.round(vitaPercentage * 10) / 10;
}