import { Card, Col, Container, Row } from 'react-bootstrap';
import bg37 from 'assets/img/bg/37.png';
import { PropsWithChildren } from 'react';
import Logo from 'components/common/Logo';

interface AuthCardLayoutProps {
  logo?: boolean;
  className?: string;
}

const AuthCardLayout = ({
  logo = true,
  children
}: PropsWithChildren<AuthCardLayoutProps>) => {
  return (
    <Container fluid className="bg-body-tertiary dark__bg-gray-1200">
      <div
        className="bg-holder bg-auth-card-overlay"
        style={{ backgroundImage: `url(${bg37})` }}
      />

      <Row className="flex-center position-relative min-vh-100 g-0 py-5">
        <Col xs={12} sm={8} xl={4}>
          <Card className="border border-translucent auth-card">
            <Card.Body className="pe-md-0">
              <Row className="align-items-center gx-0 gy-7">
                <Col className="mx-auto">
                  {logo && (
                    <div className="text-center">
                      <div className="d-inline-block text-decoration-none mb-4">
                        <Logo
                          text={false}
                          width={58}
                          className="fw-bolder fs-5 d-inline-block"
                        />
                      </div>
                    </div>
                  )}
                  <div className="auth-form-box">{children}</div>
                </Col>
              </Row>
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </Container>
  );
};

export default AuthCardLayout;
