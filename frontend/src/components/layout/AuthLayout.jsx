import AssetImage from '../common/AssetImage.jsx';

export default function AuthLayout({ children, artwork = 'login.png' }) {
  return (
    <section className="_social_login_wrapper _layout_main_wrapper">
      <div className="_shape_one"><AssetImage name="shape1.svg" className="_shape_img" /><AssetImage name="dark_shape.svg" className="_dark_shape" /></div>
      <div className="_shape_two"><AssetImage name="shape2.svg" className="_shape_img" /><AssetImage name="dark_shape1.svg" className="_dark_shape _dark_shape_opacity" /></div>
      <div className="_shape_three"><AssetImage name="shape3.svg" className="_shape_img" /><AssetImage name="dark_shape2.svg" className="_dark_shape _dark_shape_opacity" /></div>
      <div className="_social_login_wrap">
        <div className="container">
          <div className="row align-items-center">
            <div className="col-xl-8 col-lg-8 col-md-12 col-sm-12">
              <div className="_social_login_left"><div className="_social_login_left_image"><AssetImage name={artwork} alt="Social app" className="_left_img" /></div></div>
            </div>
            <div className="col-xl-4 col-lg-4 col-md-12 col-sm-12">{children}</div>
          </div>
        </div>
      </div>
    </section>
  );
}
