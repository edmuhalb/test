import { useMemo, useState } from 'react';
import type {
  ColumnSort,
  ColumnDef,
  SortDirection,
  Table as TanstackTable,
  OnChangeFn,
  VisibilityState
} from '@tanstack/react-table';
import {
  flexRender,
  useReactTable,
  getCoreRowModel,
  getSortedRowModel
} from '@tanstack/react-table';

import { Pagination } from '../ui/pagination';
import { HeaderCell } from '../ui/header-cell';
import { FooterTable } from '../ui/footer-table';
import { Loader } from '../ui/loader';

interface TableProps {
  nodes: Array<any>;
  columns: Array<ColumnDef<any>>;
  onPaginationChange?: (state: { page: number; itemsPerPage: number }) => void;
  onSortChange?: (columnId: string, direction: SortDirection) => void;
  sorting?: ColumnSort;
  onInit?: (instance: TanstackTable<any>) => void;
  enabledPagination?: boolean;
  pagination?: any;
  rowClick?: (row: any, rowIndex: number) => void;
  selector?: string;
  loading?: boolean;
}

export const Table = ({
  nodes,
  columns,
  onPaginationChange,
  onSortChange,
  sorting,
  enabledPagination = true,
  pagination,
  rowClick,
  selector = '',
  loading
}: TableProps) => {
  const tableData = useMemo(() => nodes, [nodes]);
  const tableColumns = useMemo(() => columns, [columns]);
  const [columnVisibility, setColumnVisibility] = useState({});

  const onSort = (id: string, direction: 'desc' | 'asc') => () => {
    if (typeof onSortChange === 'function') {
      onSortChange(id, direction);
    }
  };

  const changeColumnsSetting: OnChangeFn<VisibilityState> = updater => {
    if (selector && typeof updater === 'function') {
      setColumnVisibility(updater);

      localStorage.setItem(
        selector,
        JSON.stringify({
          ...updater(columnVisibility)
        })
      );
    }
  };

  const table = useReactTable({
    data: tableData,
    columns: tableColumns,
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    onColumnVisibilityChange: changeColumnsSetting,
    state: { columnVisibility }
  });

  return (
    <div>
      <div className="scrollbar ms-n1 ps-1">
        {loading && <Loader />}
        <table className="phoenix-table fs-9 table">
          <thead>
            {table.getHeaderGroups().map(headerGroup => (
              <tr key={headerGroup.id}>
                {headerGroup.headers.map(
                  ({ colSpan, column, getContext, isPlaceholder }) => {
                    const { id: cellId, columnDef } = column;
                    const sortDirection = sorting?.desc ? 'asc' : 'desc';

                    return (
                      <HeaderCell
                        key={cellId}
                        colSpan={colSpan}
                        isSorting={column.getCanSort()}
                        isCurrentSortCell={sorting?.id === cellId}
                        sortDirection={sortDirection}
                        onClick={onSort(cellId, sortDirection)}
                      >
                        {isPlaceholder
                          ? null
                          : flexRender(columnDef.header, getContext())}
                      </HeaderCell>
                    );
                  }
                )}
              </tr>
            ))}
          </thead>

          <tbody>
            {table.getRowModel().rows.map((row, index) => {
              const handelRowClick = () => {
                if (typeof rowClick === 'function') {
                  rowClick(row.original, index);
                }
              };

              return (
                <tr key={row.id} onClick={handelRowClick} role="button">
                  {row.getVisibleCells().map(cell => (
                    <td
                      key={cell.id}
                      {...cell.column.columnDef.meta?.cellProps}
                    >
                      {flexRender(
                        cell.column.columnDef.cell,
                        cell.getContext()
                      )}
                    </td>
                  ))}
                </tr>
              );
            })}
          </tbody>
          <FooterTable table={table} />
        </table>
      </div>

      {enabledPagination && pagination && onPaginationChange && (
        <Pagination
          value={pagination}
          onPaginationChange={onPaginationChange}
        />
      )}
    </div>
  );
};
