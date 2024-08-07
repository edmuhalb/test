export const getSequenceNumberForRow = (rowIndex: number) => {
  const params = new URLSearchParams(document.location.search);

  const page = params.get('page') || '1';
  const perPage = params.get('per_page') || '50';

  return parseInt(page, 10) > 1
    ? rowIndex + parseInt(perPage, 10) * (parseInt(page, 10) - 1)
    : rowIndex;
};
