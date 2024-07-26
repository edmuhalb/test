import { DealColumn } from 'data/crm/deals';
import {
  useState,
  createContext,
  Dispatch,
  SetStateAction,
  PropsWithChildren,
  useContext,
  useCallback
} from 'react';
import { DropResult } from 'react-beautiful-dnd';

interface DealsContextInterface {
  dealColumns: DealColumn[];
  setDealColumns: Dispatch<SetStateAction<DealColumn[]>>;
  openAddDealModal: boolean;
  setOpenAddDealModal: Dispatch<SetStateAction<boolean>>;
  openFilterDealModal: boolean;
  setOpenFilterDealModal: Dispatch<SetStateAction<boolean>>;
  openAddStageModal: boolean;
  setOpenAddStageModal: Dispatch<SetStateAction<boolean>>;
  handleDragEnd: (result: DropResult) => void;
  handleAddStage: (formData: DealColumn) => void;
}

export const DealsContext = createContext({} as DealsContextInterface);

const DealsProvider = ({
  children,
  data
}: PropsWithChildren<{ data: DealColumn[] }>) => {
  const [dealColumns, setDealColumns] = useState(data);
  const [openAddDealModal, setOpenAddDealModal] = useState(false);
  const [openFilterDealModal, setOpenFilterDealModal] = useState(false);
  const [openAddStageModal, setOpenAddStageModal] = useState(false);

  const handleDragEnd = useCallback(
    (result: DropResult) => {
      const { source, destination } = result;

      if (destination) {
        const updatedColumns = structuredClone(dealColumns);

        const deal = updatedColumns
          .find(column => column.id === source.droppableId)
          ?.deals.splice(source.index, 1)[0];

        if (deal) {
          updatedColumns
            .find(column => column.id === destination.droppableId)
            ?.deals.splice(destination.index, 0, deal);
        }

        setDealColumns(updatedColumns);
      }
    },
    [dealColumns]
  );

  const handleAddStage = useCallback(
    (formData: DealColumn) => {
      setDealColumns([...dealColumns, formData]);
    },
    [dealColumns]
  );
  return (
    <DealsContext.Provider
      value={{
        dealColumns,
        setDealColumns,
        openAddDealModal,
        setOpenAddDealModal,
        openFilterDealModal,
        setOpenFilterDealModal,
        openAddStageModal,
        setOpenAddStageModal,
        handleDragEnd,
        handleAddStage
      }}
    >
      {children}
    </DealsContext.Provider>
  );
};

export const useDealsContext = () => useContext(DealsContext);

export default DealsProvider;
