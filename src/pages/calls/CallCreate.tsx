import Button from 'components/base/Button';
import Dropzone from 'components/base/Dropzone';
import TinymceEditor from 'components/base/TinymceEditor';
import OrganizeFormCard from 'components/cards/OrganizeFormCard';
import VariantFormCard from 'components/cards/VariantFormCard';
import PageBreadcrumb, {PageBreadcrumbItem} from 'components/common/PageBreadcrumb';
import InventoryTab from 'components/tabs/InventoryTab';
import { defaultBreadcrumbItems } from 'data/commonData';
import {Card, Col, Form, Row} from 'react-bootstrap';
import { Form as FinalForm, Field } from 'react-final-form';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faKey, faPhone} from "@fortawesome/free-solid-svg-icons";

export const breadcrumbItems: PageBreadcrumbItem[] = [
  {
    label: 'Главная',
    url: '/'
  },
  {
    label: 'Вызовы',
    url: '/calls'
  },
  {
    label: 'Добавление вызова',
    active: true
  }
];

const CallCreate = () => {
  const onSubmit = (values: any) => {
   console.log(values)
  };

  const validate = (values: any) => {
    const errors: any = {};
    if (!values.phone) {
      errors.phone = 'Введите телефон';
    }
    if (!values.description) {
      errors.description = 'Введите описание';
    }
    return errors;
  };
  return (
    <div>
      <PageBreadcrumb items={breadcrumbItems} />
      <Col xs={12} xl={8}>
        <Card>
          <Card.Body>
            <div className="d-flex flex-wrap gap-3 flex-between-end">
              <div>
                <h2 className="mb-2">Добавление вызова</h2>
              </div>
            </div>
            <FinalForm
                onSubmit={onSubmit}
                validate={validate}
                render={({handleSubmit, submitting,pristine}) => (
                    <form onSubmit={handleSubmit}>
                      <Field
                          name="phone"
                          render={({input, meta}) => (
                              <Form.Group className="mb-3 text-start">
                                <Form.Label htmlFor="password">Пароль</Form.Label>
                                <div className="form-icon-container">
                                  <Form.Control
                                      id="phone"
                                      type="phone"
                                      disabled={submitting}
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
                          name="description"
                          render={({input, meta}) => (
                              <Form.Group className="mb-3 text-start">
                                <Form.Label htmlFor="description">Описание</Form.Label>
                                <Form.Control
                                    id="description"
                                    as="textarea"
                                    rows={5}
                                    disabled={submitting}
                                    placeholder="Описание"
                                    {...input}
                                />
                                {meta.touched && meta.error && (
                                    <span className={'text-danger fs-9'}>{meta.error}</span>
                                )}
                              </Form.Group>
                          )}
                      />
                      <Button loading={submitting} disabled={submitting || pristine} variant="primary" className="w-100 mb-3" type={'submit'}>
                        Добавить
                      </Button>
                    </form>
                )}
            />
          </Card.Body>
        </Card>
      </Col>

    </div>
  );
};

export default CallCreate;
