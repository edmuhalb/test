import { Col, Row, Stack } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconProp } from '@fortawesome/fontawesome-svg-core';
import {
  faCircle,
  faSquare,
  faStar,
  faXmark
} from '@fortawesome/free-solid-svg-icons';
import { useEffect, useState } from 'react';
import { reportCalNumberByStatus } from '../../../services/ReportService';

type StatType = {
  id: number | string;
  icon: IconProp;
  title: string;
  subTitle: string;
  color: string;
};

const CallsStats = () => {
  const [stats, setStats] = useState<StatType[]>([]);

  useEffect(() => {
    const fetchData = async () => {
      const result = await reportCalNumberByStatus();

      if (result?.data == null || typeof result?.data !== 'object') {
        return;
      }

      const resultStats = [];

      if ('total' in result.data) {
        resultStats.push({
          id: 1,
          icon: faStar,
          title: `${result.data.total} вызовов`,
          subTitle: 'Всего',
          color: 'warning'
        });
      }
      if ('completed' in result.data) {
        resultStats.push({
          id: 2,
          icon: faStar,
          title: `${result.data.completed} вызовов`,
          subTitle: 'Выполено',
          color: 'success'
        });
      }
      if ('rejected' in result.data) {
        resultStats.push({
          id: 3,
          icon: faXmark,
          title: `${result.data.rejected} вызовов`,
          subTitle: 'Отменено',
          color: 'danger'
        });
      }

      setStats(resultStats);
    };

    fetchData();
  }, []);

  return (
    <Row className="align-items-center g-4">
      {stats.map(stat => (
        <Col xs={12} md="auto" key={stat.id}>
          <Stat stat={stat} />
        </Col>
      ))}
    </Row>
  );
};

const Stat = ({ stat }: { stat: StatType }) => {
  return (
    <Stack direction="horizontal" className="align-items-center">
      {/* <img src={stat.icon} alt="" height={46} width={46} /> */}
      <span
        className="fa-layers"
        style={{ minHeight: '46px', minWidth: '46px' }}
      >
        <FontAwesomeIcon
          icon={faSquare}
          size="2x"
          className={`text-${stat.color}-light dark__text-opacity-50`}
          transform="down-4 rotate--10 left-4"
        />
        <FontAwesomeIcon
          icon={faCircle}
          size="2x"
          className={`text-stats-circle-${stat.color} fa-layers-circle`}
          transform="up-4 right-3 grow-2"
        />
        <FontAwesomeIcon
          icon={stat.icon}
          size="1x"
          className={`text-${stat.color}`}
          transform="shrink-2 up-8 right-6"
        />
      </span>

      <div className="ms-3">
        <h4 className="mb-0">{stat.title}</h4>
        <p className="text-body-secondary fs-9 mb-0">{stat.subTitle}</p>
      </div>
    </Stack>
  );
};

export default CallsStats;
