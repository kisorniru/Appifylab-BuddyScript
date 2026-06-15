import { useState } from 'react';
import { Link, Navigate, useNavigate } from 'react-router-dom';
import AuthLayout from '../components/layout/AuthLayout.jsx';
import AssetImage from '../components/common/AssetImage.jsx';
import { authApi } from '../api/authApi';

export default function Login() {
  const navigate = useNavigate();
  const [form, setForm] = useState({ email: '', password: '', remember: true });
  const [error, setError] = useState('');
  const [submitting, setSubmitting] = useState(false);

  if (authApi.isAuthenticated()) return <Navigate to="/feed" replace />;

  function updateField(event) {
    const { name, value, type, checked } = event.target;
    setForm((current) => ({ ...current, [name]: type === 'checkbox' ? checked : value }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setSubmitting(true);
    setError('');
    try {
      await authApi.login(form);
      navigate('/feed', { replace: true });
    } catch (err) {
      setError(err.message || 'Login API is not connected yet.');
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <AuthLayout artwork="login.png">
      <div className="_social_login_content">
        <div className="_social_login_left_logo _mar_b28"><AssetImage name="logo.svg" className="_left_logo" /></div>
        <p className="_social_login_content_para _mar_b8">Welcome back</p>
        <h4 className="_social_login_content_title _titl4 _mar_b50">Login to your account</h4>
        <button type="button" className="_social_login_content_btn _mar_b40" onClick={() => setError('Google OAuth UI is not connected yet. Backend /user/social-login integration is ready for provider payload.')}><AssetImage name="google.svg" className="_google_img" /> <span>Or sign-in with google</span></button>
        <div className="_social_login_content_bottom_txt _mar_b40"><span>Or</span></div>
        <form className="_social_login_form" onSubmit={handleSubmit}>
          <div className="row">
            <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div className="_social_login_form_input _mar_b14">
                <label className="_social_login_label _mar_b8">Email</label>
                <input name="email" value={form.email} onChange={updateField} type="email" className="form-control _social_login_input" placeholder="Enter email address" autoComplete="email" required />
              </div>
            </div>
            <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div className="_social_login_form_input _mar_b14">
                <label className="_social_login_label _mar_b8">Password</label>
                <input name="password" value={form.password} onChange={updateField} type="password" className="form-control _social_login_input" placeholder="Enter password" autoComplete="current-password" required />
              </div>
            </div>
          </div>
          <div className="row">
            <div className="col-6 col-lg-6 col-xl-6 col-md-6">
              <div className="form-check _social_login_form_check">
                <input className="form-check-input _social_login_form_check_input" type="radio" name="remember" id="loginRemember" checked={form.remember} onChange={updateField} />
                <label className="form-check-label _social_login_form_check_label" htmlFor="loginRemember">Remember me</label>
              </div>
            </div>
            <div className="col-6 col-lg-6 col-xl-6 col-md-6">
              <div className="_social_login_form_left">
                <p className="_social_login_form_left_para">Forgot password?</p>
              </div>
            </div>
          </div>
          {error && <div className="alert alert-warning py-2">{error}</div>}
          <div className="row">
            <div className="col-lg-12 col-md-12 col-xl-12 col-sm-12">
              <div className="_social_login_form_btn _mar_t40 _mar_b60">
                <button type="submit" className="_social_login_form_btn_link _btn1" disabled={submitting}>{submitting ? 'Signing in...' : 'Login now'}</button>
              </div>
            </div>
          </div>
        </form>
        <div className="row">
          <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div className="_social_login_bottom_txt">
              <p className="_social_login_bottom_txt_para">Dont have an account? <Link to="/register">Create New Account</Link></p>
            </div>
          </div>
        </div>
      </div>
    </AuthLayout>
  );
}
