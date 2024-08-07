import PageBreadcrumb, {
  PageBreadcrumbItem
} from 'components/common/PageBreadcrumb';
import SearchBox from 'components/common/SearchBox';
import { ChangeEvent, useEffect, useState } from 'react';
import { callsIndexAPI } from '../../services/CallService';
import { Table } from '../../shared/table';
import Badge, { BadgeBg } from '../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { ColumnDef } from '@tanstack/react-table';
import { currencyFormat } from '../../helpers/utils';

const Calls = () => {
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
    const fetchCalls = async () => {
      setIsLoading(true);
      const result = await callsIndexAPI({ page, search });
      setItems(result?.data?.items);
      setPagination(result?.data?.pagination);
      setIsLoading(false);
    };

    fetchCalls();
  }, [page, search]);

  const defaultBreadcrumbItems: PageBreadcrumbItem[] = [
    {
      label: 'Главная',
      url: '/'
    },
    {
      label: 'Вызовы',
      active: true
    }
  ];

  const statuses = {
    not_ready: {
      label: 'Не готов',
      icon: 'info',
      type: 'secondary'
    },
    waiting: {
      label: 'Ожидает',
      icon: 'info',
      type: 'secondary'
    },
    assigned: {
      label: 'Назначен',
      icon: 'info',
      type: 'primary'
    },
    accepted: {
      label: 'Принят',
      icon: 'info',
      type: 'primary'
    },
    dispatched: {
      label: 'Выехали',
      icon: 'info',
      type: 'primary'
    },
    arrived: {
      label: 'Прибыли',
      icon: 'info',
      type: 'primary'
    },
    completed: {
      label: 'Завершен',
      icon: 'info',
      type: 'success'
    },
    rejected: {
      label: 'Отклонен',
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
      header: 'Адрес',
      accessorKey: 'address',
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
      accessorKey: 'price',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Дата',
      accessorKey: 'dateTime',
      enableSorting: false
    },
    {
      header: 'Время завершения',
      accessorKey: 'completedAt',
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
        <h2 className="mb-4">Вызовы</h2>
        <div className="mb-4">
          <div className="d-flex flex-wrap gap-3">
            <SearchBox placeholder="Поиск" onChange={handleSearchInputChange} />
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

export default Calls;
