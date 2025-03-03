import type { ComponentProps } from 'react';
import { useId } from 'react';
import { useSearchParams } from 'react-router-dom';
import dayjs from 'dayjs';
import DatePicker from 'react-datepicker';
import 'dayjs/locale/ru';
import cn from 'classnames';

dayjs.locale('ru');

export type Nullable<T> = T | null;

interface InputDateRangeFilterProps
  extends Omit<
    ComponentProps<typeof DatePicker>,
    'onChange' | 'selected' | 'name'
  > {
  nameStart: string;
  nameEnd: string;
  label?: string;
  gutterBottom?: boolean;
  isClearable?: boolean;
  dateFormat?: string;
  placeholder?: string;
  readOnly?: boolean;
  invalid?: boolean;
  helperText?: string;
  showWeeksRange?: boolean;
}

export const InputDateRangeFilter = ({
  nameStart,
  nameEnd,
  label,
  gutterBottom = true,
  dateFormat = 'dd.MM.yyyy',
  placeholder,
  isClearable = true,
  readOnly,
  invalid = false,
  helperText
}: InputDateRangeFilterProps) => {
  const inputId = useId();
  const [params, setParams] = useSearchParams();

  const selectedStartDate = params.get(nameStart);
  const selectedEndDate = params.get(nameEnd);

  const startDate = selectedStartDate ? new Date(selectedStartDate) : null;
  const endDate = selectedEndDate ? new Date(selectedEndDate) : null;

  const onChange = (dates: [Nullable<Date>, Nullable<Date>]) => {
    const [start, end] = dates;

    if (start) {
      params.set(nameStart, dayjs(start).startOf('day').format());
      params.set('page', '1');
    } else {
      params.delete(nameStart);
    }
    if (end) {
      params.set(nameEnd, dayjs(end).endOf('day').format());
      params.set('page', '1');
    } else {
      params.delete(nameEnd);
    }
    setParams(params, { replace: true });
  };

  return (
    <div className={cn('input-group', { 'mb-3': gutterBottom })}>
      {label && (
        <label htmlFor={inputId} className="w-100">
          {label}
          {invalid && <small className="text-danger ml-2">{helperText}</small>}
        </label>
      )}

      <DatePicker
        onChange={onChange}
        startDate={startDate}
        endDate={endDate}
        wrapperClassName="flex-grow-1"
        className={cn('form-control', { 'is-invalid': invalid })}
        dateFormat={dateFormat}
        placeholderText={placeholder || label}
        isClearable={readOnly ? false : isClearable}
        disabled={readOnly}
        portalId={inputId}
        selectsRange
      />
    </div>
  );
};
