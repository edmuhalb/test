import Avatar from 'components/base/Avatar';
import { useState } from 'react';
import { Card, Dropdown, Nav } from 'react-bootstrap';
import avatar from 'assets/img/team/72x72/avatar.webp';
import FeatherIcon from 'feather-icons-react';
import { Link } from 'react-router-dom';
import Scrollbar from 'components/base/Scrollbar';
import classNames from 'classnames';
import { useAuth } from '../../../context/useAuth';

const ProfileDropdownMenu = ({ className }: { className?: string }) => {
  const { logout } = useAuth();

  const [navItems] = useState([
    {
      label: 'Профиль',
      icon: 'user'
    }
  ]);
  return (
    <Dropdown.Menu
      align="end"
      className={classNames(
        className,
        'navbar-top-dropdown-menu navbar-dropdown-caret py-0 dropdown-profile shadow border'
      )}
    >
      <Card className="position-relative border-0">
        <Card.Body className="p-0">
          <div className="d-flex flex-column align-items-center justify-content-center gap-2 pt-4 pb-3">
            <Avatar src={avatar} size="xl" />
            <h6 className="text-body-emphasis">Jerry Seinfield</h6>
          </div>
          <div style={{ height: '4rem' }}>
            <Scrollbar>
              <Nav className="nav flex-column mb-2 pb-1">
                {navItems.map(item => (
                  <Nav.Item key={item.label}>
                    <Nav.Link href="#!" className="px-3">
                      <FeatherIcon
                        icon={item.icon}
                        size={16}
                        className="me-2 text-body"
                      />
                      <span className="text-body-highlight">{item.label}</span>
                    </Nav.Link>
                  </Nav.Item>
                ))}
              </Nav>
            </Scrollbar>
          </div>
        </Card.Body>
        <Card.Footer className="p-0 border-top border-translucent">
          <div className="px-3">
            <Link
              to="#"
              className="btn btn-phoenix-secondary d-flex flex-center w-100"
              onClick={logout}
            >
              <FeatherIcon icon="log-out" className="me-2" size={16} />
              Выход
            </Link>
          </div>
          <div className="my-2 text-center fw-bold fs-10 text-body-quaternary"></div>
        </Card.Footer>
      </Card>
    </Dropdown.Menu>
  );
};

export default ProfileDropdownMenu;
