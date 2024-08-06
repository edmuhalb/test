/* eslint-disable @typescript-eslint/no-explicit-any */
import base64_url_decode from './base64_url_decode';

const decode = <DataFromToken = any>(
  token: string,
  options: Record<string, boolean> = {}
): DataFromToken => {
  if (typeof token !== 'string') {
    throw new Error('Invalid token specified: must be a string');
  }
  const pos = options?.header === true ? 0 : 1;

  const part = token.split('.')[pos];
  if (typeof part !== 'string') {
    throw new Error(`Invalid token specified: missing part #${pos + 1}`);
  }

  try {
    // eslint-disable-next-line vars-on-top, no-var
    var decoded = base64_url_decode(part);
  } catch (e: any) {
    throw new Error(
      `Invalid token specified: invalid base64 for part #${pos + 1} (${
        e.message
      })`
    );
  }

  try {
    // eslint-disable-next-line block-scoped-var
    return JSON.parse(decoded);
  } catch (e: any) {
    throw new Error(
      `Invalid token specified: invalid json for part #${pos + 1} (${
        e.message
      })`
    );
  }
};

export default decode;
