import type { HTMLAttributes } from 'react';
import cn from 'classnames';

interface SpinnerProps extends HTMLAttributes<HTMLDivElement> {
  color?: 'primary';
  size?: 'sm';
  variant?: 'border' | 'grow';
}

export const Spinner = ({
  color = 'primary',
  size,
  variant = 'border',
  className,
  ...otherSpinnerProps
}: SpinnerProps) => (
  <div
    className={cn('font-weight-normal', className, {
      [`spinner-${variant}`]: variant,
      [`text-${color}`]: color,
      [`size-${variant}-${size}`]: size
    })}
    role="status"
    {...otherSpinnerProps}
  >
    <span className="sr-only">Loading...</span>
  </div>
);
