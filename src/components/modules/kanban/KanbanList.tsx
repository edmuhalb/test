import classNames from 'classnames';
import Button from 'components/base/Button';
import { KanbanBoardItem } from 'data/kanban';
import React, { useState } from 'react';
import KanbanListItemCard from './KanbanListItemCard';
import { Form } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPlus } from '@fortawesome/free-solid-svg-icons';
import KanbanListHeader from './KanbanListHeader';
import { Draggable } from 'react-beautiful-dnd';

interface KanbanListProps {
  list: KanbanBoardItem;
}

const KanbanList = ({ list }: KanbanListProps) => {
  const [collapsed, setCollapsed] = useState(!!list.isCollapsed);
  return (
    <div
      className={classNames('kanban-column scrollbar', {
        collapsed
      })}
    >
      <KanbanListHeader
        list={list}
        collapsed={collapsed}
        setCollapsed={setCollapsed}
      />
      <div className="kanban-items-container">
        {list.tasks.map((task, index) => (
          <div
            className="py-2 px-2 border-bottom border-translucent"
            key={task.id}
          >
            <Draggable
              key={`${list.id}-${task.id}`}
              draggableId={`${list.id}-${task.id}`}
              index={index}
            >
              {(provided, snapshot) => (
                <>
                  <div
                    ref={provided.innerRef}
                    {...provided.draggableProps}
                    {...provided.dragHandleProps}
                  >
                    <KanbanListItemCard
                      list={list}
                      task={task}
                      className={classNames({
                        'bg-body-emphasis': snapshot.isDragging
                      })}
                    />
                  </div>
                </>
              )}
            </Draggable>
          </div>
        ))}
      </div>
      <div className="py-3 px-4 kanban-add-task">
        <Button className="bg-body-tertiary me-2 px-0">
          <FontAwesomeIcon
            icon={faPlus}
            className="text-white dark__text-gray-400"
            transform="grow-4 down-1"
          />
        </Button>
        <Form.Control
          className="search-input rounded-3 px-3"
          placeholder="Add new task"
        />
      </div>
    </div>
  );
};

export default KanbanList;
