import Badge, { BadgeBg } from '../../../components/base/Badge';
import FeatherIcon from 'feather-icons-react';
import { currencyFormat } from '../../../helpers/utils';
import { CSSProperties } from 'react';

interface CallCardProps {
  call: any;
  getStatus: (val: any) => { label: string; icon: string; type: string };
  onClick: (call: any) => void;
}

const statusColors: Record<string, string> = {
  primary: '#3874ff',
  success: '#25b003',
  danger: '#e63757',
  warning: '#e5780b',
  secondary: '#a0aec0'
};

const cardStyle: CSSProperties = {
  background: '#fff',
  border: '1px solid #e3e6ed',
  borderRadius: '0.75rem',
  padding: '1rem 1.125rem',
  marginBottom: '0.75rem',
  cursor: 'pointer',
  boxShadow: '0 2px 6px rgba(0,0,0,0.06)',
  position: 'relative',
  overflow: 'hidden',
  paddingLeft: '1.375rem'
};

const stripStyle = (color: string): CSSProperties => ({
  position: 'absolute',
  left: 0,
  top: 0,
  bottom: 0,
  width: '4px',
  background: color,
  borderRadius: '4px 0 0 4px'
});

const headerStyle: CSSProperties = {
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  marginBottom: '0.625rem'
};

const idStyle: CSSProperties = {
  fontSize: '0.75rem',
  color: '#9fa6bc',
  fontWeight: 700,
  letterSpacing: '0.02em'
};

const nameStyle: CSSProperties = {
  fontWeight: 700,
  fontSize: '0.9375rem',
  color: '#222834',
  marginBottom: '0.25rem',
  lineHeight: 1.3
};

const phoneStyle: CSSProperties = {
  fontSize: '0.8125rem',
  color: '#3874ff',
  marginBottom: '0.375rem',
  fontWeight: 500
};

const infoRowStyle: CSSProperties = {
  display: 'flex',
  alignItems: 'center',
  gap: '1rem',
  marginBottom: '0.25rem'
};

const dateStyle: CSSProperties = {
  fontSize: '0.75rem',
  color: '#9fa6bc',
  display: 'flex',
  alignItems: 'center',
  gap: '0.25rem'
};

const commentStyle: CSSProperties = {
  fontSize: '0.75rem',
  color: '#9fa6bc',
  marginTop: '0.375rem',
  lineHeight: 1.4,
  overflow: 'hidden',
  display: '-webkit-box',
  WebkitLineClamp: 2,
  WebkitBoxOrient: 'vertical' as const
};

const footerStyle: CSSProperties = {
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  gap: '0.75rem',
  paddingTop: '0.625rem',
  marginTop: '0.375rem',
  borderTop: '1px solid #e3e6ed'
};

const priceLabelStyle: CSSProperties = {
  fontSize: '0.6875rem',
  color: '#9fa6bc',
  display: 'block',
  marginBottom: '0.0625rem'
};

const priceStyle: CSSProperties = {
  fontSize: '0.875rem',
  fontWeight: 700,
  color: '#222834'
};

const rewardStyle: CSSProperties = {
  fontSize: '0.875rem',
  fontWeight: 700,
  color: '#25b003'
};

const CallCard = ({ call, getStatus, onClick }: CallCardProps) => {
  const status = getStatus(call.status);
  const stripColor = statusColors[status.type] || statusColors.secondary;
  const distance = call.mkadDistance != null ? Number(call.mkadDistance) : null;

  return (
    <div style={cardStyle} onClick={() => onClick(call)}>
      <div style={stripStyle(stripColor)} />
      <div style={headerStyle}>
        <span style={idStyle}>#{call.id}</span>
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
      <div style={{ marginBottom: '0.375rem' }}>
        <div style={nameStyle}>{call.fio || 'Без имени'}</div>
        {call.phone && <div style={phoneStyle}>{call.phone}</div>}
        <div style={infoRowStyle}>
          {call.dateTime && (
            <div style={dateStyle}>
              <FeatherIcon icon="calendar" size={13} />
              {call.dateTime}
            </div>
          )}
          {distance !== null && (
            <div style={dateStyle}>
              <FeatherIcon icon="map-pin" size={13} />
              {distance} км
            </div>
          )}
        </div>
        {call.createdAt && (
          <div style={{ ...infoRowStyle, marginTop: '0.125rem' }}>
            <div style={dateStyle}>
              <FeatherIcon icon="clock" size={13} />
              Создан: {call.createdAt}
            </div>
          </div>
        )}
        {call.completedAt && (
          <div style={{ ...infoRowStyle, marginTop: '0.125rem' }}>
            <div style={dateStyle}>
              <FeatherIcon icon="check-circle" size={13} />
              Завершён: {call.completedAt}
            </div>
          </div>
        )}
        {call.comment && <div style={commentStyle}>{call.comment}</div>}
      </div>
      <div style={footerStyle}>
        <div>
          <span style={priceLabelStyle}>Сумма</span>
          <span style={priceStyle}>{currencyFormat(call.price || 0)}</span>
        </div>
        <div>
          <span style={priceLabelStyle}>Начислено</span>
          <span style={rewardStyle}>{currencyFormat(call.reward || 0)}</span>
        </div>
      </div>
    </div>
  );
};

export default CallCard;
