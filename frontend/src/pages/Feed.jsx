import { useCallback, useState } from 'react';
import {
  Composer,
  FeedTimeline,
  Header,
  LayoutSwitch,
  LeftSidebar,
  MobileBottomNav,
  RightSidebar,
  Stories,
  ToastAlert,
} from '../components/feed/FeedPageSections.jsx';

export default function Feed() {
  const [isDark, setIsDark] = useState(false);
  const [feedRefreshKey, setFeedRefreshKey] = useState(0);
  const [toast, setToast] = useState(null);
  const closeToast = useCallback(() => setToast(null), []);
  const showToast = useCallback((nextToast) => {
    setToast({ id: Date.now(), ...nextToast });
  }, []);

  return (
    <div className={`_layout _layout_main_wrapper${isDark ? ' _dark_wrapper' : ''}`}>
      <ToastAlert toast={toast} onClose={closeToast} />
      <LayoutSwitch isDark={isDark} onToggle={() => setIsDark((dark) => !dark)} />
      <div className="_main_layout">
        <Header />
        <MobileBottomNav />
        <div className="container _custom_container">
          <div className="_layout_inner_wrap">
            <div className="row">
              <div className="col-xl-3 col-lg-3 col-md-12 col-sm-12">
                <LeftSidebar />
              </div>
              <div className="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div className="_layout_middle_wrap">
                  <div className="_layout_middle_inner">
                    <Stories />
                    <Composer onToast={showToast} onPostCreated={() => setFeedRefreshKey((key) => key + 1)} />
                    <FeedTimeline key={feedRefreshKey} />
                  </div>
                </div>
              </div>
              <div className="col-xl-3 col-lg-3 col-md-12 col-sm-12">
                <RightSidebar />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
