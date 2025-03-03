import React from 'react';
import Chart from './Chart';

const TotalsChart = () => {
  return (
    <>
      <div>
        <h3>Доход за год</h3>
        <p className="mb-1 text-body-tertiary">
          Доход от вызовов и стационара за год по месяцам
        </p>
      </div>
      <Chart />
    </>
  );
};

export default TotalsChart;
