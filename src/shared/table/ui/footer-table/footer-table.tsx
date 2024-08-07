import type { Table } from '@tanstack/react-table';
import { flexRender } from '@tanstack/react-table';

interface FooterTableProps {
  table: Table<any>;
}

export const FooterTable = ({ table }: FooterTableProps) => (
  <tfoot>
    {table.getFooterGroups().map(footerGroup => (
      <tr key={footerGroup.id}>
        <th colSpan={1}>{null}</th>

        {footerGroup.headers.map(
          ({ colSpan, id, isPlaceholder, getContext, column }) => (
            <th key={id} colSpan={colSpan}>
              {isPlaceholder
                ? null
                : flexRender(column.columnDef.footer, getContext())}
            </th>
          )
        )}
      </tr>
    ))}
  </tfoot>
);
