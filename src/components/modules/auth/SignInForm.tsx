import { faKey, faPhone } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Button from 'components/base/Button';
import { Col, Form, Row } from 'react-bootstrap';

const SignInForm = () => {
  return (
    <>
      <div className="text-center mb-7">
        <h3 className="text-body-highlight">Вход</h3>
        <p className="text-body-tertiary">
          Авторизуйтесь для доступа к аккаунту
        </p>
      </div>

      <Form.Group className="mb-3 text-start">
        <Form.Label htmlFor="email">Телефон</Form.Label>
        <div className="form-icon-container">
          <Form.Control id="phone" type="phone" className="form-icon-input" />
          <FontAwesomeIcon
            icon={faPhone}
            className="text-body fs-9 form-icon"
          />
        </div>
      </Form.Group>
      <Form.Group className="mb-3 text-start">
        <Form.Label htmlFor="password">Пароль</Form.Label>
        <div className="form-icon-container">
          <Form.Control
            id="password"
            type="password"
            className="form-icon-input"
            placeholder="Password"
          />
          <FontAwesomeIcon icon={faKey} className="text-body fs-9 form-icon" />
        </div>
      </Form.Group>
      <Row className="flex-between-center mb-7">
        <Col xs="auto">
          <Form.Check type="checkbox" className="mb-0">
            <Form.Check.Input
              type="checkbox"
              name="remember-me"
              id="remember-me"
              defaultChecked
            />
            <Form.Check.Label htmlFor="remember-me" className="mb-0">
              Запомнить меня
            </Form.Check.Label>
          </Form.Check>
        </Col>
        <Col xs="auto"></Col>
      </Row>
      <Button variant="primary" className="w-100 mb-3">
        Войти
      </Button>
      <div className="text-center"></div>
    </>
  );
};

export default SignInForm;
