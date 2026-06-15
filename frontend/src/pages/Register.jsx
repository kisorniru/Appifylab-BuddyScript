import { useState } from 'react';
import { Link, Navigate, useNavigate } from 'react-router-dom';
import AssetImage from '../components/common/AssetImage.jsx';
import { authApi } from '../api/authApi';

export default function Register() {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    passwordConfirm: '',
    terms: true,
  });
  const [error, setError] = useState('');
  const [submitting, setSubmitting] = useState(false);

  if (authApi.isAuthenticated()) return <Navigate to="/feed" replace />;

  function updateField(event) {
    const { name, value, type, checked } = event.target;
    setForm((current) => ({ ...current, [name]: type === 'checkbox' || type === 'radio' ? checked : value }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setSubmitting(true);
    setError('');

    try {
      await authApi.register(form);
      navigate('/feed', { replace: true });
    } catch (err) {
      setError(err.message || 'Registration API is not connected yet.');
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <section className="_social_registration_wrapper _layout_main_wrapper">
      <div className="_shape_one">
        <AssetImage name="shape1.svg" className="_shape_img" />
        <AssetImage name="dark_shape.svg" className="_dark_shape" />
      </div>
      <div className="_shape_two">
        <AssetImage name="shape2.svg" className="_shape_img" />
        <AssetImage name="dark_shape1.svg" className="_dark_shape _dark_shape_opacity" />
      </div>
      <div className="_shape_three">
        <AssetImage name="shape3.svg" className="_shape_img" />
        <AssetImage name="dark_shape2.svg" className="_dark_shape _dark_shape_opacity" />
      </div>
      <div className="_social_registration_wrap">
        <div className="container">
          <div className="row align-items-center">
            <div className="col-xl-8 col-lg-8 col-md-12 col-sm-12">
              <div className="_social_registration_right">
                <div className="_social_registration_right_image">
                  <AssetImage name="registration.png" alt="Image" />
                </div>
                <div className="_social_registration_right_image_dark">
                  <AssetImage name="registration1.png" alt="Image" />
                </div>
              </div>
            </div>
            <div className="col-xl-4 col-lg-4 col-md-12 col-sm-12">
              <div className="_social_registration_content">
                <div className="_social_registration_right_logo _mar_b28">
                  <AssetImage name="logo.svg" alt="Image" className="_right_logo" />
                </div>
                <p className="_social_registration_content_para _mar_b8">Get Started Now</p>
                <h4 className="_social_registration_content_title _titl4 _mar_b50">Registration</h4>
                <button type="button" className="_social_registration_content_btn _mar_b40" onClick={() => setError('Google registration is not connected yet.')}>
                  <AssetImage name="google.svg" alt="Image" className="_google_img" /> <span>Register with google</span>
                </button>
                <div className="_social_registration_content_bottom_txt _mar_b40">
                  <span>Or</span>
                </div>
                <form className="_social_registration_form" onSubmit={handleSubmit}>
                  <div className="row">
                    <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                      <div className="_social_registration_form_input _mar_b14">
                        <label className="_social_registration_label _mar_b8">First Name</label>
                        <input name="firstName" value={form.firstName} onChange={updateField} className="form-control _social_registration_input" autoComplete="given-name" required />
                      </div>
                    </div>
                    <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                      <div className="_social_registration_form_input _mar_b14">
                        <label className="_social_registration_label _mar_b8">Last Name</label>
                        <input name="lastName" value={form.lastName} onChange={updateField} className="form-control _social_registration_input" autoComplete="family-name" />
                      </div>
                    </div>
                    <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                      <div className="_social_registration_form_input _mar_b14">
                        <label className="_social_registration_label _mar_b8">Email</label>
                        <input name="email" value={form.email} onChange={updateField} type="email" className="form-control _social_registration_input" autoComplete="email" required />
                      </div>
                    </div>
                    <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                      <div className="_social_registration_form_input _mar_b14">
                        <label className="_social_registration_label _mar_b8">Password</label>
                        <input name="password" value={form.password} onChange={updateField} type="password" className="form-control _social_registration_input" autoComplete="new-password" required />
                      </div>
                    </div>
                    <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                      <div className="_social_registration_form_input _mar_b14">
                        <label className="_social_registration_label _mar_b8">Repeat Password</label>
                        <input name="passwordConfirm" value={form.passwordConfirm} onChange={updateField} type="password" className="form-control _social_registration_input" autoComplete="new-password" required />
                      </div>
                    </div>
                  </div>
                  <div className="row">
                    <div className="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                      <div className="form-check _social_registration_form_check">
                        <input className="form-check-input _social_registration_form_check_input" type="radio" name="terms" id="registrationTerms" checked={form.terms} onChange={updateField} required />
                        <label className="form-check-label _social_registration_form_check_label" htmlFor="registrationTerms">I agree to terms & conditions</label>
                      </div>
                    </div>
                  </div>
                  {error && <div className="alert alert-warning py-2 mt-3">{error}</div>}
                  <div className="row">
                    <div className="col-lg-12 col-md-12 col-xl-12 col-sm-12">
                      <div className="_social_registration_form_btn _mar_t40 _mar_b60">
                        <button type="submit" className="_social_registration_form_btn_link _btn1" disabled={submitting}>{submitting ? 'Creating...' : 'Register'}</button>
                      </div>
                    </div>
                  </div>
                </form>
                <div className="row">
                  <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div className="_social_registration_bottom_txt">
                      <p className="_social_registration_bottom_txt_para">Already have an account? <Link to="/login">Login</Link></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
