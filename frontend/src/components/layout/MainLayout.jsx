import Header from './Header.jsx';

export default function MainLayout({ left, children, right }) {
  return (
    <div className="_layout _layout_main_wrapper">
      <div className="_main_layout">
        <Header />
        <div className="container _custom_container">
          <div className="row">
            <aside className="col-xl-3 col-lg-3 d-none d-lg-block">{left}</aside>
            <main className="col-xl-6 col-lg-6 col-md-12">{children}</main>
            <aside className="col-xl-3 col-lg-3 d-none d-xl-block">{right}</aside>
          </div>
        </div>
      </div>
    </div>
  );
}
