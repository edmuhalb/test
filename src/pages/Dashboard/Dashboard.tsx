import { Col, Row } from 'react-bootstrap';
import TotalsChart from './TotalsChart/TotalsChart';
import CallsStats from './CallsStats/CallsStats';
import CountCalls from './CountCalls/CountCalls';
import CountHospitals from './CountHospitals/CountHospitals';
import CallsMap from './CallsMap/CallsMap';
import { useEffect, useState } from 'react';
import { callsIndexAPI } from '../../services/CallService';

const Dashboard = () => {
  const [calls, setCalls] = useState<any[]>([]);

  useEffect(() => {
    const fetchData = async () => {
      const response = await callsIndexAPI({ per_page: 100 });

      if (typeof response?.data?.items === 'object') {
        const result: any[] = [];

        response.data.items.forEach((item: any, index: number) => {
          if (item.location !== null) {
            result.push({
              id: index,
              lat: item.location[0],
              lng: item.location[1],
              name: item.fio,
              street: item.address,
              location: ''
            });
          }
        });
        setCalls(result);
      }
    };

    fetchData().then();
  }, []);

  return (
    <>
      <div className="pb-5">
        <Row className="g-4">
          <Col xs={12} xxl={6}>
            <div className="mb-8">
              <h2 className="mb-2">Рабочий стол</h2>
              <h5 className="text-body-tertiary fw-semibold">
                Вот что происходит в вашем бизнесе прямо сейчас
              </h5>
            </div>
            <CallsStats />
            <hr className="bg-body-secondary mb-6 mt-4" />
            <TotalsChart />
          </Col>
          <Col xs={12} xxl={6}>
            <Row className="g-3">
              <Col xs={12} md={12}>
                <CountCalls />
              </Col>
              <Col xs={12} md={12}>
                <CountHospitals />
              </Col>
            </Row>
          </Col>
        </Row>
      </div>
      <div className="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-7 border-y"></div>
      <Row className="gx-6">
        <Col xs={12} xl={12}>
          <div className="mx-n4 mx-lg-n6 ms-xl-0 h-100">
            <div className="h-100 w-100" style={{ minHeight: 500 }}>
              <CallsMap data={calls} />
            </div>
          </div>
        </Col>
      </Row>
    </>
  );
};

export default Dashboard;
