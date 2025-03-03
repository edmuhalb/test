import type { ComponentProps } from 'react';
import { useSearchParams } from 'react-router-dom';
import type { SingleValue, MultiValue } from 'react-select';
import { Option } from '../models/entities';
import { Select } from './select';

interface SelectFilterFieldProps
  extends Omit<ComponentProps<typeof Select>, 'onChange' | 'value'> {
  name: string;
}

export const SelectFilterField = ({
  name,
  options,
  isMulti,
  ...otherSelectProps
}: SelectFilterFieldProps) => {
  const [params, setParams] = useSearchParams();
  const selectValue = params.get(name);

  const getSelectedValue = (selectedOpt: string | null) => {
    if (!selectedOpt) return;

    const option = options.find((opt: Option) => opt.value === selectedOpt);

    if (!option) return;

    // eslint-disable-next-line consistent-return
    return isMulti ? [option] : option;
  };

  const setMultiValue = (selectedOptions: MultiValue<Option>) => {
    if (selectedOptions.length > 0) {
      params.set('page', '1');
      const values = selectedOptions.map(opt => opt.value);
      params.set(name, values.toString());
    } else {
      params.delete(name);
    }

    setParams(params, { replace: true });
  };

  const setSingleValue = (selectedOption: SingleValue<Option>) => {
    if (!selectedOption) {
      params.delete(name);
    } else {
      params.set('page', '1');
      params.set(name, selectedOption.value.toString());
    }
    setParams(params, { replace: true });
  };

  const onChange = (selected: SingleValue<Option> | MultiValue<Option>) => {
    if (Array.isArray(selected)) {
      setMultiValue(selected);
    } else {
      setSingleValue(selected as SingleValue<Option>);
    }
  };

  return (
    <Select
      onChange={onChange}
      options={options}
      value={getSelectedValue(selectValue) as any}
      isMulti={isMulti}
      {...otherSelectProps}
    />
  );
};
