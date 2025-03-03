import type { CSSObjectWithLabel } from 'react-select';

export const indicatorContainerStyles = (base: CSSObjectWithLabel) => ({
  ...base,
  padding: '0 8px',
  '& [class*=indicatorContainer]': {
    padding: 0
  },
  '& [class*=indicatorSeparator]': {
    margin: '8px'
  },
  '& [class*=indicatorContainer] svg': {
    width: '15px',
    height: '15px'
  }
});
