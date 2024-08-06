import { UserProfile } from '../models/User';
import { createContext, useContext, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { loginAPI } from '../services/AuthService';
import axios from 'axios';

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
        console.log(res);
        if (res) {
          localStorage.setItem('token', res?.data.token);
          const userObj = {
            id: res?.data.id,
            name: res?.data.name,
            phone: res?.data.name
          };
          localStorage.setItem('user', JSON.stringify(userObj));
          setUser(userObj);
          setToken(res?.data.token);
          navigate('/');
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
