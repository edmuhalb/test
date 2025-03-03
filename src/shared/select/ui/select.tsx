import type { CSSProperties } from 'react';
import { useId } from 'react';
import ReactSelect from 'react-select';
import type { GroupBase, Props, PropsValue } from 'react-select';
import { Option } from '../models/entities';
import { indicatorContainerStyles } from '../models/config';
import cn from 'classnames';

const defaultIsMulti = false;

interface SelectProps<IsMulti extends boolean>
  extends Props<Option, IsMulti, GroupBase<Option>> {
  label?: string;
  gutterBottom?: boolean;
  readOnly?: boolean;
  invalid?: boolean;
  helperText?: string;
  selectSize?: 'sm' | 'lg';
  options: Array<Option>;
  value?: PropsValue<Option>;
  style?: CSSProperties;
}

export const Select = <IsMulti extends boolean = typeof defaultIsMulti>({
  onChange,
  value,
  label,
  options,
  placeholder,
  gutterBottom = true,
  isClearable = true,
  readOnly = false,
  invalid = false,
  helperText,
  selectSize,
  ...otherSelectProps
}: SelectProps<IsMulti>) => {
  const selectId = useId();

  return (
    <div
      className={cn('input-group', {
        'mb-3': gutterBottom,
        [`input-group-${selectSize}`]: selectSize
      })}
    >
      {label && (
        <label className="w-100" htmlFor={selectId}>
          {label}

          {invalid && <small className="text-danger ml-2">{helperText}</small>}
        </label>
      )}

      <ReactSelect
        id={selectId}
        onChange={onChange}
        value={value}
        options={options}
        isDisabled={readOnly}
        placeholder={placeholder || label}
        className={cn('w-100', { 'is-invalid': invalid })}
        isClearable={isClearable}
        noOptionsMessage={() => 'Список пуст'}
        styles={{
          indicatorsContainer: indicatorContainerStyles
        }}
        {...otherSelectProps}
      />
    </div>
  );
};
