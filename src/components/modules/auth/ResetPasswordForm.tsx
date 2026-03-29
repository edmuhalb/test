import { faKey } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Form as FinalForm, Field } from 'react-final-form';
import Button from 'components/base/Button';
import { Form } from 'react-bootstrap';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import {
  checkCallStatusAPI,
  resetPasswordAPI
} from '../../../services/AuthService';
import { useEffect, useState, useRef } from 'react';

const ResetPasswordForm = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const checkId = searchParams.get('check_id') || '';
  const callPhone = searchParams.get('call_phone') || '';

  const [confirmed, setConfirmed] = useState(false);
  const [polling, setPolling] = useState(true);
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

  useEffect(() => {
    if (!checkId || confirmed) return;

    const poll = async () => {
      const res = await checkCallStatusAPI(checkId);
      const status = res?.data?.status;
      if (status === 'confirmed') {
        setConfirmed(true);
        setPolling(false);
        if (intervalRef.current) clearInterval(intervalRef.current);
      } else if (status === 'expired') {
        setPolling(false);
        if (intervalRef.current) clearInterval(intervalRef.current);
      }
    };

    intervalRef.current = setInterval(poll, 3000);
    return () => {
      if (intervalRef.current) clearInterval(intervalRef.current);
    };
  }, [checkId, confirmed]);

  const onSubmit = async (values: any) => {
    const res = await resetPasswordAPI(checkId, values.password);
    if (res?.data?.success) {
      navigate('/login');
    }
  };

  const validate = (values: any) => {
    const errors: any = {};
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

  if (!checkId) {
    return (
      <div className="text-center">
        <p className="text-body-tertiary">
          Сначала запросите восстановление на странице восстановления
        </p>
        <Link to="/forgot-password" className="fw-semibold">
          Восстановить пароль
        </Link>
      </div>
    );
  }

  if (!confirmed) {
    return (
      <div className="text-center">
        <h3 className="text-body-highlight mb-4">Подтвердите звонком</h3>
        <p className="text-body-tertiary mb-2">
          Позвоните на этот номер (звонок бесплатный):
        </p>
        <p
          className="fw-bold mb-4"
          style={{ fontSize: '1.5rem', letterSpacing: '1px' }}
        >
          {callPhone}
        </p>
        {polling ? (
          <p className="text-body-tertiary">
            <span
              className="spinner-border spinner-border-sm me-2"
              role="status"
            />
            Ожидаем ваш звонок...
          </p>
        ) : (
          <div>
            <p className="text-danger mb-3">
              Время ожидания истекло. Попробуйте снова.
            </p>
            <Link to="/forgot-password" className="fw-semibold">
              Повторить
            </Link>
          </div>
        )}
        <div className="mt-4">
          <Link to="/login" className="fs-9 fw-semibold">
            Вернуться к входу
          </Link>
        </div>
      </div>
    );
  }

  return (
    <>
      <div className="text-center mb-7">
        <h3 className="text-body-highlight">Новый пароль</h3>
        <p className="text-body-tertiary">
          Звонок подтверждён! Задайте новый пароль.
        </p>
      </div>

      <FinalForm
        onSubmit={onSubmit}
        validate={validate}
        render={({ handleSubmit, submitting }) => (
          <form onSubmit={handleSubmit}>
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
