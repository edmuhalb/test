import { Modal, Form } from 'react-bootstrap';
import Badge, { BadgeBg } from '../../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { currencyFormat } from '../../../helpers/utils';

interface CallDetailModalProps {
  call: any | null;
  show: boolean;
  onHide: () => void;
  getStatus: (val: any) => { label: string; icon: string; type: string };
}

const CallDetailModal = ({
  call,
  show,
  onHide,
  getStatus
}: CallDetailModalProps) => {
  if (!call) return null;

  const status = getStatus(call.status);

  return (
    <Modal show={show} onHide={onHide} centered fullscreen="sm-down">
      <Modal.Body className="p-4">
        <div className="mb-3">
          <span
            onClick={onHide}
            style={{
              cursor: 'pointer',
              display: 'inline-flex',
              alignItems: 'center',
              gap: '0.375rem',
              fontSize: '0.875rem',
              fontWeight: 600,
              color: 'var(--phoenix-primary, #3874ff)'
            }}
          >
            <FeatherIcon icon="arrow-left" size={16} />
            Назад
          </span>
        </div>

        <h2 className="mb-2">Вызов #{call.id}</h2>

        <div className="d-flex justify-content-between align-items-center mb-3">
          <Form.Label className="mb-0">Статус</Form.Label>
          <Badge
            bg={status.type as BadgeBg | undefined}
            variant="phoenix"
            iconPosition="end"
            className="fs-10"
            icon={
              <FeatherIcon icon={status.icon} size={12.8} className="ms-1" />
            }
          >
            {status.label}
          </Badge>
        </div>

        {call.fio && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>ФИО</Form.Label>
            <Form.Control type="text" value={call.fio} readOnly plaintext />
          </Form.Group>
        )}

        {call.phone && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Телефон</Form.Label>
            <Form.Control type="text" value={call.phone} readOnly plaintext />
          </Form.Group>
        )}

        {call.dateTime && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Дата вызова</Form.Label>
            <Form.Control
              type="text"
              value={call.dateTime}
              readOnly
              plaintext
            />
          </Form.Group>
        )}

        {call.createdAt && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Дата создания</Form.Label>
            <Form.Control
              type="text"
              value={call.createdAt}
              readOnly
              plaintext
            />
          </Form.Group>
        )}

        {call.completedAt && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Время завершения</Form.Label>
            <Form.Control
              type="text"
              value={call.completedAt}
              readOnly
              plaintext
            />
          </Form.Group>
        )}

        {call.mkadDistance != null && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Расстояние</Form.Label>
            <Form.Control
              type="text"
              value={`${call.mkadDistance} км`}
              readOnly
              plaintext
            />
          </Form.Group>
        )}

        {call.price > 0 && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Сумма вызова</Form.Label>
            <Form.Control
              type="text"
              value={currencyFormat(call.price)}
              readOnly
              plaintext
            />
          </Form.Group>
        )}

        {call.reward > 0 && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Начислено</Form.Label>
            <Form.Control
              type="text"
              value={currencyFormat(call.reward)}
              readOnly
              plaintext
              className="text-success fw-semibold"
            />
          </Form.Group>
        )}

        {call.comment && (
          <Form.Group className="mb-3 text-start">
            <Form.Label>Комментарий</Form.Label>
            <Form.Control
              as="textarea"
              rows={3}
              value={call.comment}
              readOnly
              plaintext
            />
          </Form.Group>
        )}

        <button
          type="button"
          className="btn btn-phoenix-secondary w-100"
          onClick={onHide}
        >
          Закрыть
        </button>
      </Modal.Body>
    </Modal>
  );
};

export default CallDetailModal;
