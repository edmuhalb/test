import React from 'react';
import { Col, Row } from 'react-bootstrap';
import Chart from './Chart';

const TotalCalls = () => {
  return (
    <>
      <Row className="justify-content-between align-items-center mb-4 g-3">
        <Col xs="auto">
          <h3>Projection vs actual</h3>
          <p className="mb-1 text-body-tertiary">
            Actual earnings vs projected earnings
          </p>
        </Col>
        <Col xs={8} sm={4}></Col>
      </Row>
      <Chart height="300px" width="100%" />
    </>
  );
};

export default TotalCalls;
