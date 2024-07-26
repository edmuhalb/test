import Button from 'components/base/Button';
import Logo from 'components/common/Logo';
import ThemeToggler from 'components/common/ThemeToggler';
import React, { useEffect, useRef } from 'react';
import { Nav, Navbar } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import FeatherIcon from 'feather-icons-react';
import classNames from 'classnames';

const NavbarMain = () => {
  const navItems: string[] = [
    'Flight',
    'Hotel',
    'Event',
    'Package',
    'Trending'
  ];
  const containerRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    const toggleShadowClass = () => {
      if (window.scrollY > 300) {
        containerRef.current?.classList.add('navbar-shadow');
      } else {
        containerRef.current?.classList.remove('navbar-shadow');
      }
    };

    document.addEventListener('scroll', () => toggleShadowClass());

    return () => document.removeEventListener('scroll', toggleShadowClass);
  }, []);
  return (
    <div className="bg-body-emphasis sticky-top" ref={containerRef}>
      <Navbar expand="lg" className="navbar-landing container-medium">
        <Navbar.Brand
          as={Link}
          to="/"
          className="flex-1 flex-lg-grow-0 me-lg-13"
        >
          <Logo />
        </Navbar.Brand>
        <div className="d-flex align-items-center gap-2 gap-sm-3 gap-md-4 my-2 order-lg-1">
          <ThemeToggler />
          <Button
            to="#!"
            as={Link}
            variant="link"
            className="text-body-tertiary p-0"
          >
            <FeatherIcon icon="map-pin" size={18} className="" />
          </Button>
          <Button
            to="#!"
            as={Link}
            variant="link"
            className="text-body-tertiary p-0"
          >
            <FeatherIcon icon="bell" size={20} className="" />
          </Button>
          <Button
            to="#!"
            as={Link}
            variant="link"
            className="text-body-tertiary p-0 me-2 me-lg-0"
          >
            <FeatherIcon icon="user" size={20} className="" />
          </Button>
        </div>

        <Navbar.Toggle className="fs-8 ps-1 ps-sm-3 pe-0 border-0">
          <span className="navbar-toggler-icon" />
        </Navbar.Toggle>
        <Navbar.Collapse id="navbarSupportedContent">
          <Nav as="ul" className="me-auto mt-3 mt-lg-0">
            {navItems.map((item, index) => (
              <Nav.Item
                as="li"
                key={index}
                className={classNames({
                  'border-bottom border-translucent border-bottom-lg-0':
                    index !== navItems.length - 1
                })}
              >
                <Nav.Link as={Link} to="#!">
                  {item}
                </Nav.Link>
              </Nav.Item>
            ))}
          </Nav>
        </Navbar.Collapse>
      </Navbar>
    </div>
  );
};

export default NavbarMain;
