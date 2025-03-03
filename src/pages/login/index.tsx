import SignInForm from 'components/modules/auth/SignInForm';
import AuthCardLayout from 'layouts/AuthCardLayout';

const LoginPage = () => {
  return (
    <AuthCardLayout className="pb-md-7">
      <SignInForm />
    </AuthCardLayout>
  );
};

export default LoginPage;
