import PageBreadcrumb, {
  PageBreadcrumbItem
} from 'components/common/PageBreadcrumb';
import SearchBox from 'components/common/SearchBox';
import { ChangeEvent, useEffect, useState } from 'react';
import { Table } from '../../shared/table';
import Badge, { BadgeBg } from '../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { ColumnDef } from '@tanstack/react-table';
import { currencyFormat } from '../../helpers/utils';
import { hospitalsIndexAPI } from '../../services/HospitalService';
import { InputDateRangeFilter } from '../../shared/ui/datepicker';
import { SelectFilterField } from '../../shared/select';
import { useSearchParams } from 'react-router-dom';

export const HOSPITAL_STATUS_OPTIONS = [
  {
    label: 'Назначен',
    value: 'assigned'
  },
  {
    label: 'В стационаре',
    value: 'inpatient'
  },
  {
    label: 'Выписан',
    value: 'completed'
  },
  {
    label: 'Отменен',
    value: 'cancelled'
  }
];

export const entries = (params: URLSearchParams) =>
  Object.fromEntries(params.entries());

const HospitalListPage = () => {
  const [params] = useSearchParams();
  const handleSearchInputChange = (e: ChangeEvent<HTMLInputElement>) => {
    setSearch(e.target.value);
  };

  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [isLoading, setIsLoading] = useState<boolean>(false);

  const [items, setItems] = useState([]);
  const [pagination, setPagination] = useState({
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
      const result = await hospitalsIndexAPI({
        page,
        search,
        ...entries(params)
      });
      setItems(result?.data?.items);
      setPagination(result?.data?.pagination);
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

  const statuses = {
    assigned: {
      label: 'Назначен',
      icon: 'info',
      type: 'secondary'
    },
    inpatient: {
      label: 'В стационаре',
      icon: 'info',
      type: 'primary'
    },
    completed: {
      label: 'Выписан',
      icon: 'info',
      type: 'success'
    },
    cancelled: {
      label: 'Отменен',
      icon: 'info',
      type: 'danger'
    }
  };

  const getStatus = (val: keyof typeof statuses) => {
    if (val in statuses) {
      return statuses[val];
    }
    return {
      label: '-',
      icon: 'info',
      type: 'warning'
    };
  };

  const columns: Array<ColumnDef<any>> = [
    {
      header: '#',
      accessorKey: 'number',
      enableSorting: false
    },
    {
      header: 'ФИО',
      accessorKey: 'fio',
      enableSorting: false,
      meta: {
        cellProps: { className: 'fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Статус',
      accessorKey: 'status',
      enableSorting: false,
      cell: ({ getValue }) => {
        const status = getStatus(getValue<any>());

        return (
          <Badge
            bg={status.type as BadgeBg | undefined}
            variant="phoenix"
            iconPosition="end"
            className="fs-10"
            icon={
              <FeatherIcon icon={status.icon} size={12.8} className="ms-1" />
            }
          >
            {status.label}
          </Badge>
        );
      },
      meta: {
        headerProps: {
          style: { width: '12%', minWidth: 200 },
          className: 'pe-3'
        }
      }
    },
    {
      header: 'Сумма',
      accessorKey: 'amount',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Дата госпитализации',
      accessorKey: 'hospitalizedAt',
      enableSorting: false
    },
    {
      header: 'Дата выписки',
      accessorKey: 'dischargedAt',
      enableSorting: false
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
              <SearchBox
                className={'w-100'}
                placeholder="Поиск"
                onChange={handleSearchInputChange}
              />
            </div>
            <div className="col-md-auto">
              <InputDateRangeFilter
                nameStart="dischargedAt[after]"
                nameEnd="dischargedAt[before]"
                placeholder="Дата выписки"
                showWeeksRange
              />
            </div>
            <div className="col-md-auto" style={{ minWidth: '300px' }}>
              <SelectFilterField
                name="status"
                placeholder="Статус"
                options={HOSPITAL_STATUS_OPTIONS}
                isMulti
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

export default HospitalListPage;
