import { faChevronRight } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import React from 'react';
import { Card } from 'react-bootstrap';
import Button from 'components/base/Button';

import { Link } from 'react-router-dom';
import HolidaysNextMonth from 'components/charts/e-charts/HolidaysNextMonth';

export const HolidaysCard = () => {
  return (
    <Card className="h-100">
      <Card.Header className="border-0 pb-2 d-flex justify-content-between align-items-start">
        <div>
          <h3 className="text-body-highlight">Holidays</h3>
          <p className="mb-sm-0 text-body-tertiary">Holidays next month</p>
        </div>
        <Link to="#!"></Link>
        <Button
          variant="phoenix-secondary"
          size="sm"
          as={Link}
          to={`#!`}
          endIcon={
            <FontAwesomeIcon icon={faChevronRight} className="ms-2 fs-10" />
          }
        >
          Calender
        </Button>
      </Card.Header>
      <Card.Body>
        <HolidaysNextMonth
          style={{ height: '100%', minHeight: '300px', width: '100%' }}
        />
      </Card.Body>
    </Card>
  );
};
