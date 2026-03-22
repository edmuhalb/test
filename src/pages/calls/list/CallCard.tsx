import Badge, { BadgeBg } from '../../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { currencyFormat } from '../../../helpers/utils';

interface CallCardProps {
  call: any;
  getStatus: (val: string) => { label: string; icon: string; type: string };
  onClick: (call: any) => void;
}

const CallCard = ({ call, getStatus, onClick }: CallCardProps) => {
  const status = getStatus(call.status);

  return (
    <div className="call-card" onClick={() => onClick(call)}>
      <div className="call-card__header">
        <span className="call-card__id">#{call.id}</span>
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
      <div className="call-card__body">
        <div className="call-card__name">{call.fio}</div>
        <div className="call-card__phone">{call.phone}</div>
        {call.dateTime && (
          <div className="call-card__date">
            <FeatherIcon icon="calendar" size={14} className="me-1" />
            {call.dateTime}
          </div>
        )}
      </div>
      {(call.price > 0 || call.reward > 0) && (
        <div className="call-card__footer">
          {call.price > 0 && (
            <span className="call-card__price">
              {currencyFormat(call.price)}
            </span>
          )}
          {call.reward > 0 && (
            <span className="call-card__reward">
              +{currencyFormat(call.reward)}
            </span>
          )}
        </div>
      )}
    </div>
  );
};

export default CallCard;
