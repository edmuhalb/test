import { Modal } from 'react-bootstrap';
import Badge, { BadgeBg } from '../../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { currencyFormat } from '../../../helpers/utils';

interface CallDetailModalProps {
  call: any | null;
  show: boolean;
  onHide: () => void;
  getStatus: (val: string) => { label: string; icon: string; type: string };
}

const DetailRow = ({
  label,
  value
}: {
  label: string;
  value: React.ReactNode;
}) => {
  if (!value) return null;
  return (
    <div className="call-detail__row">
      <span className="call-detail__label">{label}</span>
      <span className="call-detail__value">{value}</span>
    </div>
  );
};

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
      <Modal.Header closeButton className="border-bottom">
        <Modal.Title className="fs-7">Вызов #{call.id}</Modal.Title>
      </Modal.Header>
      <Modal.Body className="p-0">
        <div className="call-detail">
          <div className="call-detail__section">
            <div className="d-flex justify-content-between align-items-center mb-3">
              <span className="fw-bold fs-8">Статус</span>
              <Badge
                bg={status.type as BadgeBg | undefined}
                variant="phoenix"
                iconPosition="end"
                className="fs-10"
                icon={
                  <FeatherIcon
                    icon={status.icon}
                    size={12.8}
                    className="ms-1"
                  />
                }
              >
                {status.label}
              </Badge>
            </div>
          </div>

          <div className="call-detail__section">
            <h6 className="call-detail__section-title">Пациент</h6>
            <DetailRow label="ФИО" value={call.fio} />
            <DetailRow label="Телефон" value={call.phone} />
          </div>

          <div className="call-detail__section">
            <h6 className="call-detail__section-title">Детали вызова</h6>
            <DetailRow label="Дата вызова" value={call.dateTime} />
            <DetailRow label="Дата создания" value={call.createdAt} />
            <DetailRow label="Время завершения" value={call.completedAt} />
            <DetailRow
              label="Сумма вызова"
              value={
                call.price > 0 ? (
                  <span className="fw-semibold">
                    {currencyFormat(call.price)}
                  </span>
                ) : null
              }
            />
            <DetailRow
              label="Начислено"
              value={
                call.reward > 0 ? (
                  <span className="fw-semibold text-success">
                    {currencyFormat(call.reward)}
                  </span>
                ) : null
              }
            />
            <DetailRow label="Расстояние" value={call.mkadDistance} />
          </div>

          {call.comment && (
            <div className="call-detail__section">
              <h6 className="call-detail__section-title">Комментарий</h6>
              <p className="mb-0 text-body">{call.comment}</p>
            </div>
          )}
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default CallDetailModal;
