import { faKey, faPhone } from '@fortawesome/free-solid-svg-icons';
import { Form as FinalForm, Field } from 'react-final-form';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Button from 'components/base/Button';
import { Col, Form, Row } from 'react-bootstrap';
import { useAuth } from '../../../context/useAuth';

const SignInForm = () => {
  const { loginUser } = useAuth();

  const onSubmit = (values: any) => {
    loginUser(values.username.replace(/\D/g, ''), values.password);
  };

  const validate = (values: any) => {
    const errors: any = {};
    if (!values.username) {
      errors.username = 'Введите телефон';
    }
    if (!values.password) {
      errors.password = 'Введите пароль';
    }
    return errors;
  };

  return (
    <>
      <div className="text-center mb-7">
        <h3 className="text-body-highlight">Вход</h3>
        <p className="text-body-tertiary">
          Авторизуйтесь для доступа к аккаунту
        </p>
      </div>

      <FinalForm
        onSubmit={onSubmit}
        validate={validate}
        render={({ handleSubmit }) => (
          <form onSubmit={handleSubmit}>
            <Field
              name="username"
              render={({ input, meta }) => (
                <Form.Group className="mb-3 text-start">
                  <Form.Label htmlFor="password">Пароль</Form.Label>
                  <div className="form-icon-container">
                    <Form.Control
                      id="username"
                      type="input"
                      className="form-icon-input"
                      placeholder="Телефон"
                      {...input}
                    />
                    <FontAwesomeIcon
                      icon={faPhone}
                      className="text-body fs-9 form-icon"
                    />
                  </div>
                  {meta.touched && meta.error && (
                    <span className={'text-danger fs-9'}>{meta.error}</span>
                  )}
                </Form.Group>
              )}
            />
            <Field
              name="password"
              render={({ input, meta }) => (
                <Form.Group className="mb-3 text-start">
                  <Form.Label htmlFor="password">Пароль</Form.Label>
                  <div className="form-icon-container">
                    <Form.Control
                      id="password"
                      type="password"
                      className="form-icon-input"
                      placeholder="Парлоль"
                      {...input}
                    />
                    <FontAwesomeIcon
                      icon={faKey}
                      className="text-body fs-9 form-icon"
                    />
                  </div>
                  {meta.touched && meta.error && (
                    <span className={'text-danger fs-9'}>{meta.error}</span>
                  )}
                </Form.Group>
              )}
            />
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
            <Button variant="primary" className="w-100 mb-3" type={'submit'}>
              Войти
            </Button>
          </form>
        )}
      />
    </>
  );
};

export default SignInForm;
