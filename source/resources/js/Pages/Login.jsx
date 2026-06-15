import { useForm } from '@inertiajs/react';
import LoginForm from '../Components/Login/LoginForm';
import LandingPage from '../Templates/Landing/LandingPage';

export default function Login() {
  const { data, setData, post, processing, errors } = useForm({
    email: "",
    password: ""
  });

  const handleSubmit = (e) => {
    e.preventDefault();

    post('/login');
  }

  return (
    <LandingPage title="Login">
      <LoginForm data={data} setData={setData} processing={processing} errors={errors} handleSubmit={handleSubmit} />
    </LandingPage>
  );
}
