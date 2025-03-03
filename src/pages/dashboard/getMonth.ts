export const getMonth = () => {
  const currentDate = new Date();
  const months = [];
  let start = currentDate.getMonth() + 1;
  if (start > 11) {
    start = 0;
  }

  for (let i = start; i < 12; i++) {
    const month = new Date(currentDate.getFullYear(), i).toLocaleString(
      'ru-RU',
      {
        month: 'long'
      }
    );

    months.push(month.charAt(0).toUpperCase() + month.slice(1));
  }

  for (let i = 0; i < start; i++) {
    const month = new Date(currentDate.getFullYear(), i).toLocaleString(
      'ru-RU',
      {
        month: 'long'
      }
    );

    months.push(month.charAt(0).toUpperCase() + month.slice(1));
  }
  return months;
};
