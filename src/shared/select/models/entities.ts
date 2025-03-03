type SelectOption<Value = number, Label = string> = {
  value: Value;
  label: Label;
};

export type Option = SelectOption<any, any>;
