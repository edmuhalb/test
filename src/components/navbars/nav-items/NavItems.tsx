import Avatar from 'components/base/Avatar';
import { Dropdown, Modal, Nav } from 'react-bootstrap';
import avatar57 from 'assets/img/team/40x40/57.webp';
import ProfileDropdownMenu from './ProfileDropdownMenu';
import { useAppContext } from 'providers/AppProvider';
import FeatherIcon from 'feather-icons-react';
import { Link } from 'react-router-dom';
import ThemeToggler from 'components/common/ThemeToggler';
import { useState } from 'react';
import DropdownSearchBox from 'components/common/DropdownSearchBox';
import SearchResult from 'components/common/SearchResult';
import classNames from 'classnames';

const NavItems = () => {
  const {
    config: { navbarPosition }
  } = useAppContext();
  const [openSearchModal, setOpenSearchModal] = useState(false);

  return (
    <div className="navbar-nav navbar-nav-icons flex-row">
      <Nav.Item>
        <ThemeToggler className="px-2" />
      </Nav.Item>
      <Nav.Item
        className={classNames({
          'd-lg-none':
            navbarPosition === 'vertical' || navbarPosition === 'dual'
        })}
      >
        <Nav.Link onClick={() => setOpenSearchModal(!openSearchModal)}>
          <FeatherIcon icon="search" size={19} style={{ marginBottom: 2 }} />
        </Nav.Link>
      </Nav.Item>
      <Nav.Item>
        <Dropdown autoClose="outside" className="h-100">
          <Dropdown.Toggle
            as={Link}
            to="#!"
            className="dropdown-caret-none nav-link pe-0 py-0 lh-1 h-100 d-flex align-items-center"
            variant=""
          >
            <Avatar src={avatar57} size="l" />
          </Dropdown.Toggle>
          <ProfileDropdownMenu />
        </Dropdown>
      </Nav.Item>

      <Modal
        show={openSearchModal}
        onHide={() => setOpenSearchModal(false)}
        className="search-box-modal mt-15"
      >
        <Modal.Body className="p-0 bg-transparent">
          <DropdownSearchBox
            className="navbar-top-search-box"
            inputClassName="rounded-pill"
            size="lg"
            style={{ width: 'auto' }}
          >
            <SearchResult />
          </DropdownSearchBox>
        </Modal.Body>
      </Modal>
    </div>
  );
};

export default NavItems;
