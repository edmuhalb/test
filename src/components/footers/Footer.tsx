import classNames from 'classnames';
import { Col, Row } from 'react-bootstrap';

interface FooterProps {
  className?: string;
}

const Footer = ({ className }: FooterProps) => {
  return (
    <footer className={classNames(className, 'footer')}>
      <Row className="g-1 justify-content-between align-items-center h-100">
        <Col xs={12} sm="auto" className="text-center">
          <p className="mb-0 mt-2 mt-sm-0 text-body">
            <span className="d-none d-sm-inline-block" />
            <br className="d-sm-none" />
            {new Date().getFullYear()} &copy;{' '}
            <a href="https://reset-med.ru" target="_blank" rel="noreferrer">
              Ресет
            </a>
          </p>
        </Col>
        <Col xs={12} sm="auto" className="text-center">
          <p className="mb-0 text-body-tertiary text-opacity-85">
            v{process.env.REACT_APP_VERSION}
          </p>
        </Col>
      </Row>
    </footer>
  );
};

export default Footer;
