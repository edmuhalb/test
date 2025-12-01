import PageBreadcrumb, {
  PageBreadcrumbItem
} from 'components/common/PageBreadcrumb';

export const entries = (params: URLSearchParams) =>
  Object.fromEntries(params.entries());

const NotificationsPage = () => {
  const defaultBreadcrumbItems: PageBreadcrumbItem[] = [
    {
      label: 'Главная',
      url: '/'
    },
    {
      label: 'Уведомления',
      active: true
    }
  ];

  return (
    <div>
      <PageBreadcrumb items={defaultBreadcrumbItems} />
      <div className="mb-9">
        <h2 className="mb-4">Уведомления</h2>
        <div className="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1"></div>
      </div>
    </div>
  );
};

export default NotificationsPage;
