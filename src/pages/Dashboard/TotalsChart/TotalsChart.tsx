import React from 'react';
import Chart from './Chart';

const EcomReturningCustomerRate = () => {
  return (
    <>
      <div>
        <h3>Returning customer rate</h3>
        <p className="mb-1 text-body-tertiary">
          Rate of customers returning to your shop over time
        </p>
      </div>
      <Chart />
    </>
  );
};

export default EcomReturningCustomerRate;
