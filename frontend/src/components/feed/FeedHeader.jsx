import { useCallback, useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { authApi } from '../../api/authApi.js';
import { feedApi } from '../../api/feedApi.js';
import { useInfiniteApi } from '../../hooks/useInfiniteApi.js';
import AssetImage from '../common/AssetImage.jsx';
import { DotsIcon, DropdownArrowIcon, DropdownIcon, NavIcon, OnlineDot, SearchIcon } from './FeedIcons.jsx';
import { formatFeedTime, normalizeNotification } from './feedUtils.js';

export function LayoutSwitch({ isDark, onToggle }) {
  return (
    <div className="_layout_mode_swithing_btn">
      <button type="button" className="_layout_swithing_btn_link" onClick={onToggle} aria-label={isDark ? 'Switch to light mode' : 'Switch to dark mode'}>
        <div className="_layout_swithing_btn">
          <div className="_layout_swithing_btn_round" />
        </div>
        <div className="_layout_change_btn_ic1">
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="16" fill="none" viewBox="0 0 11 16">
            <path fill="#fff" d="M2.727 14.977l.04-.498-.04.498zm-1.72-.49l.489-.11-.489.11zM3.232 1.212L3.514.8l-.282.413zM9.792 8a6.5 6.5 0 00-6.5-6.5v-1a7.5 7.5 0 017.5 7.5h-1zm-6.5 6.5a6.5 6.5 0 006.5-6.5h1a7.5 7.5 0 01-7.5 7.5v-1z" />
          </svg>
        </div>
        <div className="_layout_change_btn_ic2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="4.389" stroke="#fff" />
            <path stroke="#fff" strokeLinecap="round" d="M3.444 12H1M23 12h-2.444M5.95 5.95L4.222 4.22M19.778 19.779L18.05 18.05M12 3.444V1M12 23v-2.445M18.05 5.95l1.728-1.729M4.222 19.779L5.95 18.05" />
          </svg>
        </div>
      </button>
    </div>
  );
}

export function NotificationDropdown({ open, onUnreadCountChange }) {
  const [activeFilter, setActiveFilter] = useState('all');
  const [localReadIds, setLocalReadIds] = useState(() => new Set());
  const unreadOnly = activeFilter === 'unread';
  const loadNotifications = useCallback(async (params) => {
    const response = await feedApi.getNotifications({ ...params, unreadOnly });
    if (response?.meta && Number.isFinite(Number(response.meta.unread_count))) {
      onUnreadCountChange(Number(response.meta.unread_count));
    }
    return response;
  }, [onUnreadCountChange, unreadOnly]);
  const {
    items,
    initialLoading,
    loading,
    error,
    hasMore,
    loadMore,
    refresh,
  } = useInfiniteApi(loadNotifications, { limit: 10, enabled: open });
  const notifications = items
    .map((item) => {
      const notification = normalizeNotification(item);
      return localReadIds.has(notification.id) ? { ...notification, isRead: true } : notification;
    })
    .filter((notification) => activeFilter === 'all' || !notification.isRead);

  useEffect(() => {
    if (open) refresh();
  }, [activeFilter, open, refresh]);

  async function handleNotificationClick(notification) {
    if (notification.isRead || !notification.id) return;

    const response = await feedApi.markNotificationAsRead(notification.id);
    setLocalReadIds((current) => {
      const next = new Set(current);
      next.add(notification.id);
      return next;
    });

    const unreadCount = response?.meta?.unread_count;
    if (Number.isFinite(Number(unreadCount))) {
      onUnreadCountChange(Number(unreadCount));
    } else {
      onUnreadCountChange((current) => Math.max(0, current - 1));
    }
  }

  function handleScroll(event) {
    const target = event.currentTarget;
    if (!hasMore || loading || initialLoading) return;

    if (target.scrollTop + target.clientHeight >= target.scrollHeight - 80) {
      loadMore();
    }
  }

  return (
    <div id="_notify_drop" className={`_notification_dropdown${open ? ' show' : ''}`} onScroll={handleScroll}>
      <div className="_notifications_content">
        <h4 className="_notifications_content_title">Notifications</h4>
        <div className="_notification_box_right">
          <button type="button" className="_notification_box_right_link"><DotsIcon /></button>
        </div>
      </div>
      <div className="_notifications_drop_box">
        <div className="_notifications_drop_btn_grp">
          <button
            type="button"
            className={`_notifications_btn_link${activeFilter === 'all' ? ' _notifications_btn_active' : ''}`}
            onClick={() => setActiveFilter('all')}
          >
            All
          </button>
          <button
            type="button"
            className={`_notifications_btn_link1${activeFilter === 'unread' ? ' _notifications_btn_active' : ''}`}
            onClick={() => setActiveFilter('unread')}
          >
            Unread
          </button>
        </div>
        <div className="_notifications_all">
          {initialLoading && (
            <div className="_notification_box _notification_state">
              <div className="_notification_txt">
                <p className="_notification_para">Loading notifications...</p>
              </div>
            </div>
          )}
          {!initialLoading && error && (
            <div className="_notification_box _notification_state">
              <div className="_notification_txt">
                <p className="_notification_para">Unable to load notifications.</p>
              </div>
            </div>
          )}
          {!initialLoading && !error && !notifications.length && (
            <div className="_notification_box _notification_state">
              <div className="_notification_txt">
                <p className="_notification_para">No notifications yet.</p>
              </div>
            </div>
          )}
          {notifications.map((notification) => (
            <button
              type="button"
              className={`_notification_box${notification.isRead ? '' : ' _notification_unread'}`}
              key={notification.id}
              onClick={() => handleNotificationClick(notification)}
            >
              <div className="_notification_image">
                <AssetImage name={notification.senderAvatar} alt="" className="_notify_img" />
              </div>
              <div className="_notification_txt">
                <p className="_notification_para">
                  {notification.senderName && <span className="_notify_txt_link">{notification.senderName}</span>}
                  {notification.senderName ? ' ' : ''}
                  {notification.message.replace(`${notification.senderName} `, '')}
                </p>
                <div className="_nitification_time"><span>{formatFeedTime(notification.createdAt)}</span></div>
              </div>
            </button>
          ))}
          {loading && !initialLoading && (
            <div className="_notification_box _notification_state">
              <div className="_notification_txt">
                <p className="_notification_para">Loading more...</p>
              </div>
            </div>
          )}
          {!hasMore && notifications.length > 0 && (
            <div className="_notification_box _notification_state">
              <div className="_notification_txt">
                <p className="_notification_para">You are all caught up.</p>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export function Header() {
  const [notificationsOpen, setNotificationsOpen] = useState(false);
  const [unreadNotificationCount, setUnreadNotificationCount] = useState(0);
  const [profileOpen, setProfileOpen] = useState(false);
  const [loggingOut, setLoggingOut] = useState(false);
  const notificationRef = useRef(null);
  const profileRef = useRef(null);
  const navigate = useNavigate();
  const user = authApi.getStoredUser();

  useEffect(() => {
    let ignore = false;

    feedApi.getNotifications({ limit: 1 })
      .then((response) => {
        if (!ignore && Number.isFinite(Number(response?.meta?.unread_count))) {
          setUnreadNotificationCount(Number(response.meta.unread_count));
        }
      })
      .catch(() => {
        if (!ignore) setUnreadNotificationCount(0);
      });

    return () => {
      ignore = true;
    };
  }, []);

  useEffect(() => {
    function handleOutsideClick(event) {
      const target = event.target;

      if (notificationsOpen && notificationRef.current && !notificationRef.current.contains(target)) {
        setNotificationsOpen(false);
      }

      if (profileOpen && profileRef.current && !profileRef.current.contains(target)) {
        setProfileOpen(false);
      }
    }

    document.addEventListener('mousedown', handleOutsideClick);
    document.addEventListener('touchstart', handleOutsideClick);

    return () => {
      document.removeEventListener('mousedown', handleOutsideClick);
      document.removeEventListener('touchstart', handleOutsideClick);
    };
  }, [notificationsOpen, profileOpen]);

  async function handleLogout() {
    setLoggingOut(true);
    try {
      await authApi.logout();
      navigate('/login', { replace: true });
    } finally {
      setLoggingOut(false);
    }
  }

  return (
    <>
      <nav className="navbar navbar-expand-lg navbar-light _header_nav _padd_t10">
        <div className="container _custom_container">
          <div className="_logo_wrap">
            <a className="navbar-brand" href="/feed">
              <AssetImage name="logo.svg" alt="Buddy Script" className="_nav_logo" />
            </a>
          </div>
          <button className="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span className="navbar-toggler-icon" />
          </button>
          <div className="collapse navbar-collapse" id="navbarSupportedContent">
            <div className="_header_form ms-auto">
              <form className="_header_form_grp">
                <SearchIcon className="_header_form_svg" />
                <input className="form-control me-2 _inpt1" type="search" placeholder="input search text" aria-label="Search" />
              </form>
            </div>
            <ul className="navbar-nav mb-2 mb-lg-0 _header_nav_list ms-auto _mar_r8">
              <li className="_header_nav_item nav-item">
                <a className="nav-link _header_nav_link _header_nav_link_active" href="#0" aria-label="Home">
                  <NavIcon type="home" size={22} />
                </a>
              </li>
              <li className="_header_nav_item nav-item">
                <a className="nav-link _header_nav_link" href="#0" aria-label="Friends">
                  <NavIcon type="friends" size={24} />
                </a>
              </li>
              <li className="_header_nav_item nav-item" ref={notificationRef}>
                <span
                  className="nav-link _header_nav_link _header_notify_btn"
                  onClick={() => setNotificationsOpen((open) => !open)}
                  onKeyDown={(event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                      event.preventDefault();
                      setNotificationsOpen((open) => !open);
                    }
                  }}
                  role="button"
                  tabIndex={0}
                  aria-label="Notifications"
                >
                  <NavIcon type="bell" size={24} />
                  {unreadNotificationCount > 0 && <span className="_counting">{unreadNotificationCount}</span>}
                  <NotificationDropdown open={notificationsOpen} onUnreadCountChange={setUnreadNotificationCount} />
                </span>
              </li>
              <li className="_header_nav_item nav-item">
                <a className="nav-link _header_nav_link" href="#0" aria-label="Messages">
                  <NavIcon type="chat" size={24} />
                  <span className="_counting">2</span>
                </a>
              </li>
            </ul>
            <div className="_header_nav_profile" ref={profileRef}>
              <div className="_header_nav_profile_image">
                <AssetImage name={user?.avatar || 'profile.png'} alt="Profile" className="_nav_profile_img" />
              </div>
              <div className="_header_nav_dropdown">
                <p className="_header_nav_para">{user?.name || 'Dylan Field'}</p>
                <button
                  className="_header_nav_dropdown_btn _dropdown_toggle"
                  type="button"
                  aria-label="Profile menu"
                  aria-expanded={profileOpen}
                  onClick={() => setProfileOpen((open) => !open)}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" fill="none" viewBox="0 0 10 6">
                    <path fill="#112032" d="M5 5l.354.354L5 5.707l-.354-.353L5 5zm4.354-3.646l-4 4-.708-.708 4-4 .708.708zm-4.708 4l-4-4 .708-.708 4 4-.708.708z" />
                  </svg>
                </button>
              </div>
              {profileOpen && (
                <div className="_nav_profile_dropdown _profile_dropdown show">
                  <div className="_nav_profile_dropdown_info">
                    <div className="_nav_profile_dropdown_image">
                      <AssetImage name={user?.avatar || 'profile.png'} alt="Profile" className="_nav_drop_img" />
                    </div>
                    <div className="_nav_profile_dropdown_info_txt">
                      <h4 className="_nav_dropdown_title">{user?.name || 'Dylan Field'}</h4>
                      <a href="#0" className="_nav_drop_profile">View Profile</a>
                    </div>
                  </div>
                  <hr />
                  <ul className="_nav_dropdown_list">
                    <li className="_nav_dropdown_list_item">
                      <a href="#0" className="_nav_dropdown_link">
                        <div className="_nav_drop_info"><span><DropdownIcon type="settings" /></span>Settings</div>
                        <button type="button" className="_nav_drop_btn_link" aria-label="Open settings"><DropdownArrowIcon /></button>
                      </a>
                    </li>
                    <li className="_nav_dropdown_list_item">
                      <a href="#0" className="_nav_dropdown_link">
                        <div className="_nav_drop_info"><span><DropdownIcon type="help" /></span>Help &amp; Support</div>
                        <button type="button" className="_nav_drop_btn_link" aria-label="Open help and support"><DropdownArrowIcon /></button>
                      </a>
                    </li>
                    <li className="_nav_dropdown_list_item">
                      <button type="button" className="_nav_dropdown_link _bs_nav_dropdown_action" onClick={handleLogout} disabled={loggingOut}>
                        <div className="_nav_drop_info"><span><DropdownIcon type="logout" /></span>{loggingOut ? 'Logging out...' : 'Log Out'}</div>
                        <span className="_nav_drop_btn_link"><DropdownArrowIcon /></span>
                      </button>
                    </li>
                  </ul>
                </div>
              )}
            </div>
          </div>
        </div>
      </nav>

      <div className="_header_mobile_menu">
        <div className="_header_mobile_menu_wrap">
          <div className="container">
            <div className="_header_mobile_menu">
              <div className="row">
                <div className="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                  <div className="_header_mobile_menu_top_inner">
                    <div className="_header_mobile_menu_logo">
                      <a href="/feed" className="_mobile_logo_link">
                        <AssetImage name="logo.svg" alt="Buddy Script" className="_nav_logo" />
                      </a>
                    </div>
                    <div className="_header_mobile_menu_right">
                      <form className="_header_form_grp">
                        <a href="#0" className="_header_mobile_search" aria-label="Search">
                          <SearchIcon />
                        </a>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

export function MobileBottomNav() {
  return (
    <div className="_mobile_navigation_bottom_wrapper">
      <div className="_mobile_navigation_bottom_wrap">
        <div className="conatiner">
          <div className="row">
            <div className="col-xl-12 col-lg-12 col-md-12">
              <ul className="_mobile_navigation_bottom_list">
                {[
                  ['Home', 'home'],
                  ['Friends', 'friends'],
                  ['Notifications', 'bell'],
                  ['Chat', 'chat'],
                ].map(([item, type], index) => (
                  <li className="_mobile_navigation_bottom_item" key={item}>
                    <a className={`_mobile_navigation_bottom_link${index === 0 ? ' _mobile_navigation_bottom_link_active' : ''}`} href="#0" aria-label={item}>
                      <NavIcon type={type} size={24} />
                      {index > 1 && <span className="_counting">{index === 2 ? '6' : '2'}</span>}
                    </a>
                  </li>
                ))}
                <div className="_header_mobile_toggle">
                  <button type="button" className="_header_mobile_btn_link" aria-label="Open menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="14" fill="none" viewBox="0 0 18 14">
                      <path stroke="#666" strokeLinecap="round" strokeWidth="1.5" d="M1 1h16M1 7h16M1 13h16" />
                    </svg>
                  </button>
                </div>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
