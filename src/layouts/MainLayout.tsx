import classNames from 'classnames';
import Footer from 'components/footers/Footer';
import NavbarDual from 'components/navbars/navbar-dual/NavbarDual';
import NavbarTopHorizontal from 'components/navbars/navbar-horizontal/NavbarTopHorizontal';
import NavbarTopDefault from 'components/navbars/navbar-top/NavbarTopDefault';
import NavbarVertical from 'components/navbars/navbar-vertical/NavbarVertical';
import { useAppContext } from 'providers/AppProvider';
import { useMainLayoutContext } from 'providers/MainLayoutProvider';
import { Container } from 'react-bootstrap';
import { Outlet, useLocation, useNavigate } from 'react-router-dom';
import Button from '../components/base/Button';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPlusCircle } from '@fortawesome/free-solid-svg-icons';

const MainLayout = () => {
  const {
    config: { navbarPosition }
  } = useAppContext();

  const { contentClass, footerClass } = useMainLayoutContext();

  const navigate = useNavigate();
  const { pathname } = useLocation();
  return (
    <Container fluid className="px-0">
      {(navbarPosition === 'vertical' || navbarPosition === 'combo') && (
        <NavbarVertical />
      )}
      {navbarPosition === 'vertical' && <NavbarTopDefault />}
      {(navbarPosition === 'horizontal' || navbarPosition === 'combo') && (
        <NavbarTopHorizontal />
      )}
      {navbarPosition === 'dual' && <NavbarDual />}

      <div className={classNames(contentClass, 'content')}>
        <Outlet />
        <Footer className={classNames(footerClass, 'position-absolute')} />
        {pathname !== '/calls/create' && (
          <Button
            style={{ width: '14rem' }}
            className={classNames('p-0 border border-primary btn-support-chat')}
            onClick={() => navigate('/calls/create')}
          >
            <span className="fs-8 btn-text text-primary text-nowrap">
              Добавить вызов
            </span>
            <FontAwesomeIcon
              icon={faPlusCircle}
              className="text-success fs-9 ms-2"
            />
          </Button>
        )}
      </div>
    </Container>
  );
};

export default MainLayout;
