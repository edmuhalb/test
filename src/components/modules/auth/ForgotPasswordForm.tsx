import { faPhone } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Form as FinalForm, Field } from 'react-final-form';
import Button from 'components/base/Button';
import { Form } from 'react-bootstrap';
import { Link, useNavigate } from 'react-router-dom';
import { forgotPasswordAPI } from '../../../services/AuthService';
import { InputMask } from '@react-input/mask';
import { toast } from 'react-toastify';

const ForgotPasswordForm = () => {
  const navigate = useNavigate();

  const onSubmit = async (values: any) => {
    const phone = values.phone.replace(/\D/g, '');
    const res = await forgotPasswordAPI(phone);
    if (res?.data?.success) {
      toast.success('Код отправлен на ваш телефон');
      navigate(`/reset-password?phone=${encodeURIComponent(phone)}`);
    }
  };

  const validate = (values: any) => {
    const errors: any = {};
    const digits = (values.phone || '').replace(/\D/g, '');
    if (!digits || digits.length < 11) {
      errors.phone = 'Введите номер телефона';
    }
    return errors;
  };

  return (
    <>
      <div className="text-center mb-7">
        <h3 className="text-body-highlight">Восстановление пароля</h3>
        <p className="text-body-tertiary">
          Введите номер телефона и мы отправим вам SMS с кодом
        </p>
      </div>

      <FinalForm
        onSubmit={onSubmit}
        validate={validate}
        render={({ handleSubmit, submitting }) => (
          <form onSubmit={handleSubmit}>
            <Field
              name="phone"
              render={({ input, meta }) => (
                <Form.Group className="mb-3 text-start">
                  <Form.Label htmlFor="phone">Телефон</Form.Label>
                  <div className="form-icon-container">
                    <InputMask
                      id="phone"
                      mask="+_(___) ___-__-__"
                      replacement={{ _: /\d/ }}
                      className="form-icon-input form-control"
                      disabled={submitting}
                      placeholder="Телефон"
                      {...input}
                    />
                    <FontAwesomeIcon
                      icon={faPhone}
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
              Отправить код
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

export default ForgotPasswordForm;
