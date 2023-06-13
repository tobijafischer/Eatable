export default function debounce(fn: (...args: any[]) => void, delay: number): (...args: any[]) => void {
  let timeoutID: number | null = null;
  return function (...args: any[]): void {
    if (timeoutID !== null) {
      clearTimeout(timeoutID);
    }
    timeoutID = window.setTimeout(function () {
      fn(...args);
    }, delay);
  };
}