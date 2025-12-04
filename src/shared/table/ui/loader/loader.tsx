import { Spinner } from '../../../ui/spinner';

export const Loader = () => (
  <div
    className="position-absolute w-100 h-100 z-index-1050 d-flex flex-center text-center"
    style={{ backgroundColor: 'rgba(0, 0, 0, 0.075)' }}
  >
    <Spinner />
  </div>
);
