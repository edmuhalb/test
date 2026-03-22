import PageBreadcrumb, {
  PageBreadcrumbItem
} from 'components/common/PageBreadcrumb';
import SearchBox from 'components/common/SearchBox';
import { ChangeEvent, useEffect, useMemo, useState } from 'react';
import { callsIndexAPI } from '../../../services/CallService';
import { Table } from '../../../shared/table';
import Badge, { BadgeBg } from '../../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { ColumnDef } from '@tanstack/react-table';
import { currencyFormat } from '../../../helpers/utils';
import { InputDateRangeFilter } from '../../../shared/ui/datepicker';
import { SelectFilterField } from '../../../shared/select';
import { useNavigate, useSearchParams } from 'react-router-dom';
import CallCard from './CallCard';
import CallDetailModal from './CallDetailModal';
import { useBreakpoints } from '../../../providers/BreakpointsProvider';

export const CALLING_STATUSES = [
  'created',
  'waiting',
  'assigned',
  'accepted',
  'rejected',
  'arrived',
  'completed',
  'dispatched'
];

export const callingStatusFormatter = (status: string) => {
  switch (status) {
    case 'created':
      return 'Создан';
    case 'waiting':
      return 'В работе КЦ';
    case 'completed':
      return 'Завершен';
    case 'accepted':
      return 'Принят';
    case 'rejected':
      return 'Отклонен';
    case 'arrived':
      return 'Прибыли';
    case 'treating':
      return 'Лечение';
    case 'assigned':
      return 'Назначен';
    case 'dispatched':
      return 'Выехали';
    case 'repeat':
      return 'Назначенный повтор';
    case 'not_ready':
      return 'Не готов';
    default:
      return status;
  }
};

const CALLING_STATUS_OPTIONS = CALLING_STATUSES.map(status => ({
  value: status,
  label: callingStatusFormatter(status)
}));

export const entries = (params: URLSearchParams) =>
  Object.fromEntries(params.entries());

const FINISHED_STATUSES = ['rejected', 'completed'];

const tabsContainerStyle: React.CSSProperties = {
  display: 'flex',
  gap: 0,
  marginBottom: '1rem',
  background: 'var(--phoenix-emphasis-bg, #fff)',
  borderRadius: '0.5rem',
  padding: '3px',
  border: '1px solid var(--phoenix-border-color, #e3e6ed)',
  boxShadow: '0 1px 3px rgba(0,0,0,0.04)'
};

const tabBaseStyle: React.CSSProperties = {
  flex: 1,
  padding: '0.4375rem 0.75rem',
  textAlign: 'center',
  fontSize: '0.8125rem',
  fontWeight: 600,
  background: 'none',
  border: 'none',
  borderRadius: '0.375rem',
  cursor: 'pointer',
  transition: 'all 0.2s ease',
  color: 'var(--phoenix-secondary-color, #9fa6bc)'
};

const tabActiveStyle: React.CSSProperties = {
  ...tabBaseStyle,
  color: '#fff',
  background: 'var(--phoenix-primary, #3874ff)',
  boxShadow: '0 2px 8px rgba(56,116,255,0.3)'
};

const countBaseStyle: React.CSSProperties = {
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  fontSize: '0.6875rem',
  minWidth: '1.125rem',
  height: '1rem',
  borderRadius: '10px',
  padding: '0 0.3rem',
  marginLeft: '0.35rem',
  fontWeight: 700,
  background: 'rgba(0,0,0,0.1)'
};

const countActiveStyle: React.CSSProperties = {
  ...countBaseStyle,
  background: 'rgba(255,255,255,0.25)',
  color: '#fff'
};

const CallsListPage = () => {
  const [params] = useSearchParams();
  const navigate = useNavigate();
  const { breakpoints } = useBreakpoints();
  const isMobile = breakpoints.down('lg');
  const handleSearchInputChange = (e: ChangeEvent<HTMLInputElement>) => {
    setSearch(e.target.value);
  };

  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [activeTab, setActiveTab] = useState<'active' | 'finished'>('active');
  const [viewMode, setViewMode] = useState<'cards' | 'table'>('cards');
  const [selectedCall, setSelectedCall] = useState<any | null>(null);
  const [showModal, setShowModal] = useState(false);

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
      const result = await callsIndexAPI({ ...entries(params), page, search });
      setItems(result?.data?.items);
      setPagination(result?.data?.pagination);
      setIsLoading(false);
    };

    fetchCalls();
  }, [page, search, params]);

  const activeItems = useMemo(() => {
    return (
      items?.filter((item: any) => !FINISHED_STATUSES.includes(item.status)) ||
      []
    );
  }, [items]);

  const finishedItems = useMemo(() => {
    return (
      items?.filter((item: any) => FINISHED_STATUSES.includes(item.status)) ||
      []
    );
  }, [items]);

  const currentItems = activeTab === 'active' ? activeItems : finishedItems;

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
    created: {
      label: 'Создан',
      icon: 'info',
      type: 'secondary'
    },
    not_ready: {
      label: 'В работе КЦ',
      icon: 'info',
      type: 'secondary'
    },
    repeat: {
      label: 'Повтор',
      icon: 'info',
      type: 'secondary'
    },
    waiting: {
      label: 'В работе КЦ',
      icon: 'info',
      type: 'primary'
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
      label: val,
      icon: 'info',
      type: 'warning'
    };
  };

  const handleCardClick = (call: any) => {
    setSelectedCall(call);
    setShowModal(true);
  };

  const columns: Array<ColumnDef<any>> = [
    {
      header: '#',
      accessorKey: 'id',
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
      header: 'Телефон',
      accessorKey: 'phone',
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
      header: 'Сумма вызова',
      accessorKey: 'price',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Начислено',
      accessorKey: 'reward',
      enableSorting: false,
      cell: ({ getValue }) => currencyFormat(getValue<any>()),
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Расстояние',
      accessorKey: 'mkadDistance',
      enableSorting: false,
      meta: {
        headerProps: { style: { width: '6%' }, className: 'text-end' },
        cellProps: { className: 'text-end fw-semibold text-body-highlight' }
      }
    },
    {
      header: 'Комментарий',
      accessorKey: 'comment',
      enableSorting: false
    },
    {
      header: 'Дата',
      accessorKey: 'dateTime',
      enableSorting: false
    },
    {
      header: 'Дата создания',
      accessorKey: 'createdAt',
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
      {!isMobile && <PageBreadcrumb items={defaultBreadcrumbItems} />}
      <div className="mb-9">
        {isMobile ? (
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'space-between',
              marginBottom: '0.5rem'
            }}
          >
            <h2 style={{ fontSize: '1.25rem', margin: 0 }}>Вызовы</h2>
            <button
              onClick={() => navigate('/calls/create')}
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: '0.375rem',
                height: '36px',
                padding: '0 0.75rem',
                borderRadius: '0.375rem',
                border: '1px solid var(--phoenix-primary, #3874ff)',
                background: 'var(--phoenix-primary, #3874ff)',
                color: '#fff',
                cursor: 'pointer',
                fontSize: '0.8125rem',
                fontWeight: 600,
                whiteSpace: 'nowrap'
              }}
            >
              <FeatherIcon icon="plus" size={16} />
              Добавить вызов
            </button>
          </div>
        ) : (
          <h2 className="mb-4">Вызовы</h2>
        )}
        {isMobile ? (
          <div
            className="mobile-filters-compact"
            style={{
              display: 'flex',
              flexDirection: 'column',
              gap: '0.5rem',
              marginBottom: '0.75rem'
            }}
          >
            <style>{`
              .mobile-filters-compact .form-control,
              .mobile-filters-compact [class*="-control"] {
                font-size: 0.8rem !important;
                font-weight: 600 !important;
                min-height: 36px !important;
                padding-top: 0.25rem;
                padding-bottom: 0.25rem;
                border-radius: 0.375rem;
                border-color: var(--phoenix-border-color, #e3e6ed);
              }
              .mobile-filters-compact .form-control {
                height: 36px;
              }
              .mobile-filters-compact [class*="-control"] {
                height: auto !important;
              }
              .mobile-filters-compact [class*="-ValueContainer"] {
                padding-top: 2px !important;
                padding-bottom: 2px !important;
                flex-wrap: wrap !important;
              }
              .mobile-filters-compact [class*="-placeholder"] {
                font-size: 0.8rem !important;
                font-weight: 600 !important;
                color: var(--phoenix-secondary-color, #b0b7c3) !important;
              }
              .mobile-filters-compact .form-control {
                padding-left: 0.5rem !important;
              }
              .mobile-filters-compact .form-control::placeholder {
                font-size: 0.8rem !important;
                font-weight: 600 !important;
                color: var(--phoenix-secondary-color, #b0b7c3) !important;
              }
              .mobile-filters-compact [class*="-ValueContainer"] {
                padding-left: 0.5rem !important;
              }
              .mobile-filters-compact [class*="-indicatorContainer"] {
                padding: 4px !important;
              }
            `}</style>
            <InputDateRangeFilter
              nameStart="completedAt[after]"
              nameEnd="completedAt[before]"
              placeholder="Выберите период"
              showWeeksRange
              gutterBottom={false}
            />
            <div
              style={{
                display: 'flex',
                gap: '0.5rem',
                alignItems: 'stretch'
              }}
            >
              <div style={{ flex: 1, minWidth: 0 }}>
                <SelectFilterField
                  name="status"
                  placeholder="Выберите статус"
                  options={CALLING_STATUS_OPTIONS}
                  isMulti
                  gutterBottom={false}
                />
              </div>
              <div style={{ display: 'flex', gap: '4px', flexShrink: 0 }}>
                {(['cards', 'table'] as const).map(mode => (
                  <button
                    key={mode}
                    onClick={() => setViewMode(mode)}
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      width: '36px',
                      borderRadius: '0.375rem',
                      border: '1px solid var(--phoenix-border-color, #e3e6ed)',
                      background: viewMode === mode ? 'var(--phoenix-primary, #3874ff)' : 'var(--phoenix-emphasis-bg, #fff)',
                      color: viewMode === mode ? '#fff' : 'var(--phoenix-secondary-color, #9fa6bc)',
                      cursor: 'pointer',
                      transition: 'all 0.2s ease',
                      padding: 0
                    }}
                    title={mode === 'cards' ? 'Карточки' : 'Таблица'}
                  >
                    <FeatherIcon
                      icon={mode === 'cards' ? 'grid' : 'list'}
                      size={16}
                    />
                  </button>
                ))}
              </div>
            </div>
          </div>
        ) : (
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
                  nameStart="completedAt[after]"
                  nameEnd="completedAt[before]"
                  placeholder="Дата выполнения"
                  showWeeksRange
                />
              </div>
              <div className="col-md-auto">
                <SelectFilterField
                  name="status"
                  placeholder="Статус"
                  options={CALLING_STATUS_OPTIONS}
                  isMulti
                />
              </div>
              <div className="col"></div>
            </div>
          </div>
        )}

        {isMobile && viewMode === 'cards' ? (
          <>
            <div style={tabsContainerStyle}>
              <button
                style={activeTab === 'active' ? tabActiveStyle : tabBaseStyle}
                onClick={() => setActiveTab('active')}
              >
                Активные
                <span
                  style={
                    activeTab === 'active' ? countActiveStyle : countBaseStyle
                  }
                >
                  {activeItems.length}
                </span>
              </button>
              <button
                style={activeTab === 'finished' ? tabActiveStyle : tabBaseStyle}
                onClick={() => setActiveTab('finished')}
              >
                Завершенные
                <span
                  style={
                    activeTab === 'finished' ? countActiveStyle : countBaseStyle
                  }
                >
                  {finishedItems.length}
                </span>
              </button>
            </div>

            {isLoading ? (
              <div className="text-center py-4">
                <div className="spinner-border spinner-border-sm text-primary" />
              </div>
            ) : currentItems.length > 0 ? (
              currentItems.map((call: any) => (
                <CallCard
                  key={call.id}
                  call={call}
                  getStatus={getStatus}
                  onClick={handleCardClick}
                />
              ))
            ) : (
              <div className="text-center py-4 text-body-tertiary">
                Нет вызовов
              </div>
            )}
          </>
        ) : (
          <div className="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
            <Table
              nodes={items}
              columns={columns}
              loading={isLoading}
              pagination={pagination}
              onPaginationChange={onPaginationChange}
            />
          </div>
        )}
      </div>

      <CallDetailModal
        call={selectedCall}
        show={showModal}
        onHide={() => setShowModal(false)}
        getStatus={getStatus}
      />
    </div>
  );
};

export default CallsListPage;
