export const getElementOffset = (element: HTMLElement) => {
  const rect = element.getBoundingClientRect();
  const { scrollTop } = document.documentElement;
  return { top: rect.top + scrollTop };
};
