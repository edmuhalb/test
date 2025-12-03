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
import { faPlusCircle, faComment } from '@fortawesome/free-solid-svg-icons';
import ChatWidget from 'components/common/chat-widget/ChatWidget';
import { useChatWidgetContext } from 'providers/ChatWidgetProvider';

const MainLayout = () => {
  const {
    config: { navbarPosition }
  } = useAppContext();

  const { contentClass, footerClass } = useMainLayoutContext();

  const navigate = useNavigate();
  const { pathname } = useLocation();
  const { isOpenChat, setIsOpenChat } = useChatWidgetContext();

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
        <div
          className="d-flex gap-2"
          style={{
            position: 'fixed',
            bottom: '1.5rem',
            right: '1.5rem',
            zIndex: 1045
          }}
        >
          {pathname !== '/calls/create' && (
            <Button
              style={{ width: '14rem' }}
              className={classNames('border border-primary')}
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
          {false && (
            <Button
              style={{ width: '14rem' }}
              className={classNames('border border-primary')}
              onClick={() => setIsOpenChat(!isOpenChat)}
            >
              <span className="fs-8 btn-text text-primary text-nowrap">
                {isOpenChat ? 'Закрыть чат' : 'Чат'}
              </span>
              <FontAwesomeIcon
                icon={faComment}
                className="text-primary fs-9 ms-2"
              />
            </Button>
          )}
        </div>
        <ChatWidget hideButton={true} />
      </div>
    </Container>
  );
};

export default MainLayout;
