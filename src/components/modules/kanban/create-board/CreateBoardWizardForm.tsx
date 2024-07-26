import useWizardForm from 'hooks/useWizardForm';
import WizardFormProvider from 'providers/WizardFormProvider';
import { Tab } from 'react-bootstrap';
import StepProgressBoard from './StepProgressBoard';
import WizardFormFooter from 'components/wizard/WizardFormFooter';
import WizardForm from 'components/wizard/WizardForm';
import BoardForm from './BoardForm';
import ColumnForm from './ColumnForm';
import BackgroundColorForm from './BackgroundColorForm';
import TagsForm from './TagsForm';
import AccessForm from './AccessForm';

export interface CreateBoardTag {
  label: string;
  icon: string;
  color: string;
}
export interface CreateBoardFormData {
  name: string;
  type: string;
  description?: string;
  columns: {
    name: string;
    color: string;
  }[];
  backgroundColor?: string;
  backgroundImage?: string;
  tags: CreateBoardTag[];
}

const defaultColumns = [
  {
    name: 'Unassigned',
    color: '#6e7891'
  },
  {
    name: 'To Do',
    color: '#25b003'
  },
  {
    name: 'Doing',
    color: '#ec1f00'
  }
];

const defaultTags: CreateBoardTag[] = [
  {
    label: 'Feature',
    icon: 'package',
    color: 'primary'
  },
  {
    label: 'Completed',
    icon: 'check',
    color: 'success'
  },
  {
    label: 'Issue',
    icon: 'alert-triangle',
    color: 'warning'
  },
  {
    label: 'Bug',
    icon: 'alert-octagon',
    color: 'danger'
  }
];

const CreateBoardWizardForm = () => {
  const form = useWizardForm<CreateBoardFormData>(
    {
      totalStep: 5
    },
    { columns: defaultColumns, tags: defaultTags }
  );
  return (
    <WizardFormProvider {...form}>
      <StepProgressBoard className="mb-9" />
      <div className="theme-wizard">
        <Tab.Content className="mb-10">
          <Tab.Pane eventKey={1}>
            <WizardForm step={1}>
              <BoardForm />
            </WizardForm>
          </Tab.Pane>
          <Tab.Pane eventKey={2}>
            <WizardForm step={2}>
              <ColumnForm />
            </WizardForm>
          </Tab.Pane>
          <Tab.Pane eventKey={3}>
            <WizardForm step={3}>
              <BackgroundColorForm />
            </WizardForm>
          </Tab.Pane>
          <Tab.Pane eventKey={4}>
            <WizardForm step={4}>
              <TagsForm />
            </WizardForm>
          </Tab.Pane>
          <Tab.Pane eventKey={5}>
            <WizardForm step={5}>
              <AccessForm />
            </WizardForm>
          </Tab.Pane>
        </Tab.Content>

        <WizardFormFooter
          nextBtnLabel={form.getCanNextPage ? 'Next' : 'Submit'}
        />
      </div>
    </WizardFormProvider>
  );
};

export default CreateBoardWizardForm;
