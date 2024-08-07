import type { ReactNode } from 'react';
import type { SortDirection } from '@tanstack/react-table';
import cn from 'classnames';

interface HeaderCellProps {
  isSorting: boolean;
  isCurrentSortCell: boolean;
  sortDirection: SortDirection;
  children: ReactNode;
  onClick: () => void;
  colSpan: number;
}

export const HeaderCell = ({
  isSorting,
  isCurrentSortCell,
  sortDirection,
  children,
  onClick,
  colSpan
}: HeaderCellProps) => {
  const classes = cn('text-nowrap', {
    sorting: isSorting,
    sorting_asc: sortDirection === 'asc' && isCurrentSortCell,
    sorting_desc: sortDirection === 'desc' && isCurrentSortCell
  });

  return (
    <th
      className={classes}
      onClick={isSorting ? onClick : undefined}
      colSpan={colSpan}
    >
      {children}
    </th>
  );
};
