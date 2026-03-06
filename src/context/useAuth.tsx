import { UserProfile } from '../models/User';
import { createContext, useContext, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { loginAPI } from '../services/AuthService';
import axios from 'axios';
import decode from '../helpers/jwtDecode';

type UserContextType = {
  user: UserProfile | null;
  token: string | null;
  loginUser: (username: string, password: string) => void;
  logout: () => void;
  isLoggedIn: () => boolean;
};

type Props = {
  children: React.ReactNode;
};

const UserContext = createContext<UserContextType>({} as UserContextType);

export const UserProvider = ({ children }: Props) => {
  const navigate = useNavigate();

  const [user, setUser] = useState<UserProfile | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isReady, setIsReady] = useState(false);

  useEffect(() => {
    const user = localStorage.getItem('user');
    const token = localStorage.getItem('token');
    if (user && token) {
      setUser(JSON.parse(user));
      setToken(token);
      axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
    setIsReady(true);
  }, []);

  const loginUser = async (username: string, password: string) => {
    await loginAPI(username, password)
      .then(res => {
        if (res) {
          localStorage.setItem('token', res?.data.token);

          const user = decode(res?.data.token);

          const userObj = {
            id: user.id,
            name: user.name,
            phone: user.phone,
            roles: user.roles,
            partner_id: user.partner_id,
            partner_name: user.partner_name
          };

          localStorage.setItem('user', JSON.stringify(userObj));
          setUser(userObj);
          setToken(res?.data.token);
          axios.defaults.headers.common.Authorization = `Bearer ${token}`;
          navigate('/');
          navigate(0);
        }
      })
      .catch(e => {
        console.log(e);
      });
  };

  const isLoggedIn = () => {
    return !!user;
  };

  const logout = () => {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    setUser(null);
    setToken(null);
    navigate('/login');
  };

  return (
    <UserContext.Provider
      value={{
        user,
        token,
        loginUser,
        logout,
        isLoggedIn
      }}
    >
      {isReady && children}
    </UserContext.Provider>
  );
};

export const useAuth = () => {
  return useContext(UserContext);
};
