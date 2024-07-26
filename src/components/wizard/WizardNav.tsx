import { IconProp } from '@fortawesome/fontawesome-svg-core';
import {
  faCheck,
  faFileAlt,
  faLock,
  faUser
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classNames from 'classnames';
import { useWizardFormContext } from 'providers/WizardFormProvider';
import { Nav } from 'react-bootstrap';

const WizardNavItem = ({
  icon,
  label,
  step
}: {
  icon: IconProp;
  label: string;
  step: number;
}) => {
  const { selectedStep, totalStep } = useWizardFormContext();
  return (
    <Nav.Item as="li" className="nav-item">
      <Nav.Link
        className={classNames('fw-semibold', {
          done: selectedStep > step && step !== totalStep,
          complete: selectedStep > step && step !== totalStep - 1
        })}
        eventKey={step}
      >
        <div className="text-center d-inline-block">
          <span className="nav-item-circle-parent">
            <span className="nav-item-circle">
              <FontAwesomeIcon icon={icon} />
            </span>
          </span>
          <span className="d-none d-md-block mt-1 fs-9">{label}</span>
        </div>
      </Nav.Link>
    </Nav.Item>
  );
};

const WizardNav = () => {
  return (
    <Nav className="justify-content-between nav-wizard">
      <WizardNavItem icon={faLock} step={1} label="Account" />
      <WizardNavItem icon={faUser} step={2} label="Personal" />
      <WizardNavItem icon={faFileAlt} step={3} label="Billing" />
      <WizardNavItem icon={faCheck} step={4} label="Done" />
    </Nav>
  );
};

export default WizardNav;
