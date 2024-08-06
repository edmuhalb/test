import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from './context/useAuth';

type Props = {
  children: React.ReactNode;
};

export const ProtectedRoute = ({ children }: Props) => {
  const location = useLocation();
  const { isLoggedIn } = useAuth();
  return isLoggedIn() ? (
    <>{children}</>
  ) : (
    <Navigate to="/login" state={{ from: location }} replace />
  );
};
