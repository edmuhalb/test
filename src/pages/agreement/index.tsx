import PageBreadcrumb, {
  PageBreadcrumbItem
} from 'components/common/PageBreadcrumb';
import { useEffect, useState } from 'react';
import { agreementGetAPI } from '../../services/AgreementService';
import { Card } from 'react-bootstrap';

const AgreementPage = () => {
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [agreementData, setAgreementData] = useState<any>(null);

  useEffect(() => {
    const fetchAgreement = async () => {
      setIsLoading(true);
      try {
        const result = await agreementGetAPI();
        setAgreementData(result?.data);
      } catch (error) {
        console.error('Ошибка при загрузке соглашения:', error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchAgreement();
  }, []);

  const defaultBreadcrumbItems: PageBreadcrumbItem[] = [
    {
      label: 'Главная',
      url: '/'
    },
    {
      label: 'Соглашение',
      active: true
    }
  ];

  return (
    <div>
      <PageBreadcrumb items={defaultBreadcrumbItems} />
      <div className="mb-9">
        <h2 className="mb-4">Соглашение</h2>
        {isLoading ? (
          <div className="text-center py-5">
            <div className="spinner-border" role="status">
              <span className="visually-hidden">Загрузка...</span>
            </div>
          </div>
        ) : agreementData ? (
          <Card>
            <Card.Body>
              <pre className="mb-0" style={{ whiteSpace: 'pre-wrap' }}>
                {JSON.stringify(agreementData, null, 2)}
              </pre>
            </Card.Body>
          </Card>
        ) : (
          <Card>
            <Card.Body>
              <p className="text-muted mb-0">Данные не найдены</p>
            </Card.Body>
          </Card>
        )}
      </div>
    </div>
  );
};

export default AgreementPage;

