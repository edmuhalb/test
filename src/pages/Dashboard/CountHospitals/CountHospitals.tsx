import { Card } from 'react-bootstrap';
import Chart from './Chart';
import { useEffect, useState } from 'react';
import { reportHospitalCountForTheYear } from '../../../services/ReportService';

const CountHospitals = () => {
  const [data, setData] = useState([]);
  const [total, setTotal] = useState('');

  useEffect(() => {
    const fetchData = async () => {
      const response = await reportHospitalCountForTheYear();

      if (typeof response?.data === 'object') {
        setData(response.data);
        const sum: number = response.data.reduce(
          (acc: number, curr: number) => acc + curr,
          0
        );
        setTotal(sum.toString());
      }
    };

    fetchData();
  }, []);

  return (
    <Card className="h-100">
      <Card.Body>
        <div className="d-flex justify-content-between">
          <div>
            <h5 className="mb-1">Количество стационаров</h5>
            <h6 className="text-body-tertiary">за последний год</h6>
          </div>
          <h4>{total}</h4>
        </div>
        <div className="pb-0 pt-4">
          <Chart data={data} />
        </div>
      </Card.Body>
    </Card>
  );
};

export default CountHospitals;
