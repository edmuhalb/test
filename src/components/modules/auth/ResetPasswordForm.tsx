import { faKey } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Form as FinalForm, Field } from 'react-final-form';
import Button from 'components/base/Button';
import { Form } from 'react-bootstrap';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { resetPasswordAPI } from '../../../services/AuthService';
import { toast } from 'react-toastify';

const ResetPasswordForm = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const phone = searchParams.get('phone') || '';

  const onSubmit = async (values: any) => {
    const res = await resetPasswordAPI(phone, values.code, values.password);
    if (res?.data?.success) {
      toast.success('Пароль успешно изменён');
      navigate('/login');
    }
  };

  const validate = (values: any) => {
    const errors: any = {};
    if (!values.code || values.code.length < 4) {
      errors.code = 'Введите 4-значный код из SMS';
    }
    if (!values.password) {
      errors.password = 'Введите новый пароль';
    } else if (values.password.length < 4) {
      errors.password = 'Минимум 4 символа';
    }
    if (values.password && values.confirmPassword !== values.password) {
      errors.confirmPassword = 'Пароли не совпадают';
    }
    return errors;
  };

  if (!phone) {
    return (
      <div className="text-center">
        <p className="text-body-tertiary">
          Сначала запросите код на странице восстановления
        </p>
        <Link to="/forgot-password" className="fw-semibold">
          Восстановить пароль
        </Link>
      </div>
    );
  }

  return (
    <>
      <div className="text-center mb-7">
        <h3 className="text-body-highlight">Новый пароль</h3>
        <p className="text-body-tertiary">Введите код из SMS и новый пароль</p>
      </div>

      <FinalForm
        onSubmit={onSubmit}
        validate={validate}
        render={({ handleSubmit, submitting }) => (
          <form onSubmit={handleSubmit}>
            <Field
              name="code"
              render={({ input, meta }) => (
                <Form.Group className="mb-3 text-start">
                  <Form.Label htmlFor="code">Код из SMS</Form.Label>
                  <Form.Control
                    id="code"
                    type="text"
                    inputMode="numeric"
                    maxLength={4}
                    disabled={submitting}
                    placeholder="0000"
                    autoComplete="one-time-code"
                    className="text-center fs-7 fw-bold letter-spacing-2"
                    {...input}
                  />
                  {meta.touched && meta.error && (
                    <span className="text-danger fs-9">{meta.error}</span>
                  )}
                </Form.Group>
              )}
            />
            <Field
              name="password"
              render={({ input, meta }) => (
                <Form.Group className="mb-3 text-start">
                  <Form.Label htmlFor="new-password">Новый пароль</Form.Label>
                  <div className="form-icon-container">
                    <Form.Control
                      id="new-password"
                      type="password"
                      disabled={submitting}
                      className="form-icon-input"
                      placeholder="Новый пароль"
                      autoComplete="new-password"
                      {...input}
                    />
                    <FontAwesomeIcon
                      icon={faKey}
                      className="text-body fs-9 form-icon"
                    />
                  </div>
                  {meta.touched && meta.error && (
                    <span className="text-danger fs-9">{meta.error}</span>
                  )}
                </Form.Group>
              )}
            />
            <Field
              name="confirmPassword"
              render={({ input, meta }) => (
                <Form.Group className="mb-3 text-start">
                  <Form.Label htmlFor="confirm-password">
                    Подтвердите пароль
                  </Form.Label>
                  <div className="form-icon-container">
                    <Form.Control
                      id="confirm-password"
                      type="password"
                      disabled={submitting}
                      className="form-icon-input"
                      placeholder="Подтвердите пароль"
                      autoComplete="new-password"
                      {...input}
                    />
                    <FontAwesomeIcon
                      icon={faKey}
                      className="text-body fs-9 form-icon"
                    />
                  </div>
                  {meta.touched && meta.error && (
                    <span className="text-danger fs-9">{meta.error}</span>
                  )}
                </Form.Group>
              )}
            />
            <Button
              loading={submitting}
              disabled={submitting}
              variant="primary"
              className="w-100 mb-3"
              type="submit"
            >
              Сменить пароль
            </Button>
            <div className="text-center">
              <Link to="/login" className="fs-9 fw-semibold">
                Вернуться к входу
              </Link>
            </div>
          </form>
        )}
      />
    </>
  );
};

export default ResetPasswordForm;
