import { faEllipsisH, faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Button from 'components/base/Button';
import { DealColumn as Column } from 'data/crm/deals';
import { currencyFormat } from 'helpers/utils';
import { Dropdown } from 'react-bootstrap';
import DealCard from 'components/cards/DealCard';
import { Draggable } from 'react-beautiful-dnd';

const DealColumn = ({
  column,
  handleOpenAddModal
}: {
  column: Column;
  handleOpenAddModal: () => void;
}) => {
  return (
    <div className="deals-column">
      <div className="d-flex align-items-center justify-content-between position-sticky top-0 z-1 bg-body">
        <div>
          <h5 className="mb-2">{column.title}</h5>
          <p className="fs-9 text-body-tertiary mb-1">Forecast Revenue:</p>
          <h4 className="mb-3">
            {currencyFormat(column.revenue, {
              minimumFractionDigits: 2
            })}
          </h4>
        </div>
        <div className="d-flex gap-3">
          <Button className="p-0" onClick={handleOpenAddModal}>
            <FontAwesomeIcon icon={faPlus} />
          </Button>
          <Dropdown align="end">
            <Dropdown.Toggle
              variant=""
              className="dropdown-caret-none p-0 fs-10"
            >
              <FontAwesomeIcon icon={faEllipsisH} />
            </Dropdown.Toggle>

            <Dropdown.Menu>
              <Dropdown.Item href="#">Edit</Dropdown.Item>
              <Dropdown.Item href="#">Add meeting</Dropdown.Item>
              <Dropdown.Item href="#">See all connected contacts</Dropdown.Item>
              <Dropdown.Item href="#">Clone</Dropdown.Item>
              <Dropdown.Item href="#">Delete</Dropdown.Item>
              <Dropdown.Item href="#">Display only bad deals</Dropdown.Item>
            </Dropdown.Menu>
          </Dropdown>
        </div>
      </div>
      <div className="scrollbar flex-1 d-flex flex-column">
        {column.deals.map((item, index) => (
          <Draggable key={item.id} draggableId={item.id} index={index}>
            {provided => (
              <>
                <div
                  ref={provided.innerRef}
                  {...provided.draggableProps}
                  {...provided.dragHandleProps}
                >
                  <DealCard deal={item} key={item.id} columnId={column.id} />
                </div>
              </>
            )}
          </Draggable>
        ))}
      </div>
    </div>
  );
};

export default DealColumn;
