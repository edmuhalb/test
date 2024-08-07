import { createPages } from '../../lib/create-pages';
import { showFromTo } from '../../lib/show-from-to';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
  faChevronLeft,
  faChevronRight
} from '@fortawesome/free-solid-svg-icons';
import { Pagination as PaginationComponent } from 'react-bootstrap';
interface PaginationProps {
  value: any;
  onPaginationChange: (state: { page: number; itemsPerPage: number }) => void;
}

export const Pagination = ({ value, onPaginationChange }: PaginationProps) => {
  const { page, itemsPerPage, last, pages } = value;

  const onChange = (selectedPage: number) => () => {
    onPaginationChange({
      page: selectedPage,
      itemsPerPage
    });
  };

  const pagesArray = createPages(last, page);
  const fromToString = showFromTo(value);
  const getCanPreviousPage = page > 1;
  const getCanNextPage = page === last;

  return (
    <div className="align-items-center py-1 row">
      {pages > 1 && (
        <div className="d-flex fs-9 col">
          <div className="dataTables_info">{fromToString}</div>
        </div>
      )}
      {pages > 1 && (
        <div className={'col-auto'}>
          <ul className="pagination pagination-sm col-auto ml-auto mb-0">
            <PaginationComponent.Prev
              disabled={!getCanPreviousPage}
              onClick={onChange(page - 1)}
            >
              <FontAwesomeIcon icon={faChevronLeft} />
            </PaginationComponent.Prev>

            {pagesArray.map((pageNumber: any, pageIndex: any) => {
              const pageKey = `${pageNumber}-${pageIndex}`;
              const isActive = page === pageNumber;

              return (
                <PaginationComponent.Item
                  key={pageKey}
                  active={isActive}
                  onClick={onChange(pageNumber)}
                >
                  {pageNumber}
                </PaginationComponent.Item>
              );
            })}
            <PaginationComponent.Next
              disabled={!getCanNextPage}
              onClick={onChange(page + 1)}
            >
              <FontAwesomeIcon icon={faChevronRight} />
            </PaginationComponent.Next>
          </ul>
        </div>
      )}
    </div>
  );
};
