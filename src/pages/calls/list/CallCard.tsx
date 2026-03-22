import Badge, { BadgeBg } from '../../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { currencyFormat } from '../../../helpers/utils';

interface CallCardProps {
  call: any;
  getStatus: (val: any) => { label: string; icon: string; type: string };
  onClick: (call: any) => void;
}

const CallCard = ({ call, getStatus, onClick }: CallCardProps) => {
  const status = getStatus(call.status);

  return (
    <div
      className={`call-card call-card--${status.type}`}
      onClick={() => onClick(call)}
    >
      <div className="call-card__header">
        <span className="call-card__id">#{call.id}</span>
        <Badge
          bg={status.type as BadgeBg | undefined}
          variant="phoenix"
          iconPosition="end"
          className="fs-10"
          icon={<FeatherIcon icon={status.icon} size={12.8} className="ms-1" />}
        >
          {status.label}
        </Badge>
      </div>
      <div className="call-card__body">
        <div className="call-card__name">{call.fio || 'Без имени'}</div>
        {call.phone && <div className="call-card__phone">{call.phone}</div>}
        <div className="call-card__info-row">
          {call.dateTime && (
            <div className="call-card__date">
              <FeatherIcon icon="calendar" size={13} />
              {call.dateTime}
            </div>
          )}
          {call.mkadDistance && (
            <div className="call-card__distance">
              <FeatherIcon icon="map-pin" size={13} />
              {call.mkadDistance} км
            </div>
          )}
        </div>
        {call.comment && (
          <div className="call-card__comment">{call.comment}</div>
        )}
      </div>
      {(call.price > 0 || call.reward > 0) && (
        <div className="call-card__footer">
          {call.price > 0 && (
            <div>
              <span className="call-card__price-label">Сумма</span>
              <span className="call-card__price">
                {currencyFormat(call.price)}
              </span>
            </div>
          )}
          {call.reward > 0 && (
            <div>
              <span className="call-card__reward-label">Начислено</span>
              <span className="call-card__reward">
                +{currencyFormat(call.reward)}
              </span>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default CallCard;
