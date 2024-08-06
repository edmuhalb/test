/* eslint-disable @typescript-eslint/no-explicit-any */
const chars =
  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';

function polyfill(input: any) {
  const str = String(input).replace(/=+$/, '');
  if (str.length % 4 === 1) {
    throw new Error(
      "'atob' failed: The string to be decoded is not correctly encoded."
    );
  }
  for (
    // initialize result and counters
    // eslint-disable-next-line vars-on-top, no-var
    var bc = 0, bs, buffer, idx = 0, output = '';
    // get next character
    // eslint-disable-next-line no-cond-assign, no-plusplus
    (buffer = str.charAt(idx++));
    // character found in table? initialize bit storage and add its ascii value;
    // eslint-disable-next-line no-bitwise
    ~buffer &&
    // eslint-disable-next-line no-cond-assign
    ((bs = bc % 4 ? (bs as any) * 64 + buffer : buffer),
    // and if not first of each 4 characters,
    // convert the first 8 bits to one ascii character
    // eslint-disable-next-line no-plusplus
    bc++ % 4)
      ? // eslint-disable-next-line no-bitwise
        (output += String.fromCharCode(255 & (bs >> ((-2 * bc) & 6))))
      : 0
  ) {
    // try to find character in table (0-63, not found => -1)
    buffer = chars.indexOf(buffer);
  }
  // eslint-disable-next-line block-scoped-var
  return output;
}

export default (typeof window !== 'undefined' &&
  window.atob &&
  window.atob.bind(window)) ||
  polyfill;
