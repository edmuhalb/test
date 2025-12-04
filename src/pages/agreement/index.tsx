import PageBreadcrumb, {
  PageBreadcrumbItem
} from 'components/common/PageBreadcrumb';
import { useEffect, useState } from 'react';
import { agreementGetAPI } from '../../services/AgreementService';
import { Table } from '../../shared/table';
import { ColumnDef } from '@tanstack/react-table';

interface AgreementRow {
  distance: number;
  service: {
    id: number;
    name: string;
  };
  percent: number;
  repeatNumber: number;
}

interface AgreementData {
  id: number;
  partner: {
    id: number;
    name: string;
    whatsappGroup: string | null;
  };
  startsAt: string;
  rows: AgreementRow[];
}

const AgreementPage = () => {
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [agreementData, setAgreementData] = useState<AgreementData | null>(
    null
  );

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

  const columns: Array<ColumnDef<AgreementRow>> = [
    {
      header: 'Услуга',
      accessorKey: 'service',
      enableSorting: false,
      cell: ({ getValue }) => {
        const service = getValue<AgreementRow['service']>();
        return <span className="fw-semibold text-body-highlight">{service?.name || '-'}</span>;
      },
      meta: {
        cellProps: { className: 'fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Расстояние',
      accessorKey: 'distance',
      enableSorting: false,
      cell: ({ getValue }) => {
        const distance = getValue<number>();
        return <span>{distance !== null && distance !== undefined ? `${distance} км` : '-'}</span>;
      },
      meta: {
        headerProps: { style: { width: '12%' }, className: 'text-center' },
        cellProps: { className: 'text-center' }
      }
    },
    {
      header: 'Процент',
      accessorKey: 'percent',
      enableSorting: false,
      cell: ({ getValue }) => {
        const percent = getValue<number>();
        return <span>{percent !== null && percent !== undefined ? `${percent}%` : '-'}</span>;
      },
      meta: {
        headerProps: { style: { width: '12%' }, className: 'text-center' },
        cellProps: { className: 'text-center fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Повтор',
      accessorKey: 'repeatNumber',
      enableSorting: false,
      cell: ({ getValue }) => {
        const repeatNumber = getValue<number>();
        return <span>{repeatNumber !== null && repeatNumber !== undefined ? repeatNumber : '-'}</span>;
      },
      meta: {
        headerProps: { style: { width: '12%' }, className: 'text-center' },
        cellProps: { className: 'text-center' }
      }
    }
  ];

  return (
    <div>
      <PageBreadcrumb items={defaultBreadcrumbItems} />
      <div className="mb-9">
        <div className="d-flex justify-content-between align-items-center mb-4">
          <h2 className="mb-0">Соглашение</h2>
          {agreementData?.startsAt && (
            <div className="text-body-secondary">
              <span className="fw-semibold">Дата начала действия: </span>
              <span>{agreementData.startsAt}</span>
            </div>
          )}
        </div>
        {isLoading ? (
          <div className="text-center py-5">
            <div className="spinner-border" role="status">
              <span className="visually-hidden">Загрузка...</span>
            </div>
          </div>
        ) : agreementData?.rows ? (
          <div className="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
            <Table
              nodes={agreementData.rows}
              columns={columns}
              loading={isLoading}
              enabledPagination={false}
            />
          </div>
        ) : (
          <div className="card">
            <div className="card-body">
              <p className="text-muted mb-0">Данные не найдены</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default AgreementPage;

