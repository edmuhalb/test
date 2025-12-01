import PageBreadcrumb, {
  PageBreadcrumbItem
} from 'components/common/PageBreadcrumb';
import { useEffect, useState } from 'react';
import { Table } from '../../shared/table';
import { ColumnDef } from '@tanstack/react-table';
import { currencyFormat } from '../../helpers/utils';
import { InputDateRangeFilter } from '../../shared/ui/datepicker';
import { useSearchParams } from 'react-router-dom';

export const entries = (params: URLSearchParams) =>
  Object.fromEntries(params.entries());

const ResultsPage = () => {
  const [params] = useSearchParams();

  const [page, setPage] = useState(1);
  const [search] = useState('');
  const [isLoading, setIsLoading] = useState<boolean>(false);

  const [items] = useState([]);
  const [pagination] = useState({
    first: 1,
    last: 1,
    next: 1,
    itemsPerPage: 50,
    page: 1,
    pages: 0,
    totalItems: 0
  });

  useEffect(() => {
    const fetchHospitals = async () => {
      setIsLoading(true);

      setIsLoading(false);
    };

    fetchHospitals();
  }, [page, search, params]);

  const defaultBreadcrumbItems: PageBreadcrumbItem[] = [
    {
      label: 'Главная',
      url: '/'
    },
    {
      label: 'Стационар',
      active: true
    }
  ];

  const columns: Array<ColumnDef<any>> = [
    {
      header: '#',
      accessorKey: 'number',
      enableSorting: false
    },
    {
      header: 'Дата',
      accessorKey: 'date',
      enableSorting: false
    },
    {
      header: 'Остаток на начало дня',
      accessorKey: 'sss',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Выплачено',
      accessorKey: 'sss',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Начислено выезд',
      accessorKey: 'sss',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Начислено стационар',
      accessorKey: 'sss',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Питер',
      accessorKey: 'sss',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'РЦ',
      accessorKey: 'sss',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Остаток на конец дня',
      accessorKey: 'sss',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    }
  ];

  const onPaginationChange = (state: {
    page: number;
    itemsPerPage: number;
  }) => {
    setPage(state.page);
  };

  return (
    <div>
      <PageBreadcrumb items={defaultBreadcrumbItems} />
      <div className="mb-9">
        <h2 className="mb-4">Стационар</h2>
        <div className="mb-4">
          <div className="row">
            <div className="col-md-auto">
              <InputDateRangeFilter
                nameStart="dischargedAt[after]"
                nameEnd="dischargedAt[before]"
                placeholder="Дата выписки"
                showWeeksRange
              />
            </div>
          </div>
        </div>
        <div className="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
          <Table
            nodes={items}
            columns={columns}
            loading={isLoading}
            pagination={pagination}
            onPaginationChange={onPaginationChange}
          />
        </div>
      </div>
    </div>
  );
};

export default ResultsPage;
