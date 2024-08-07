import type { ReactNode } from 'react';
import cn from 'classnames';

interface HeaderTableProps {
  title: ReactNode;
  children?: ReactNode;
  selector?: string;
  className?: string;
}

export const HeaderTable = ({
  title,
  children,
  className
}: HeaderTableProps) => {
  return (
    <div className={cn('card-header d-flex align-items-center', className)}>
      <div className="content-header px-0 mr-3 d-inline-flex align-items-center">
        <h1>{title}</h1>
      </div>
      {children}
    </div>
  );
};
