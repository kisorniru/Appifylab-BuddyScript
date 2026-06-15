import { Link, NavLink, useNavigate } from 'react-router-dom';
import { Bell, Home, LogOut, MessageCircle, Search, Settings, UserRound, UsersRound } from 'lucide-react';
import AssetImage from '../common/AssetImage.jsx';
import { authApi } from '../../api/authApi.js';

const navItems = [
  { to: '/feed', icon: Home, label: 'Feed' },
  { to: '/friends', icon: UsersRound, label: 'Friends' },
  { to: '/chat', icon: MessageCircle, label: 'Chat' },
  { to: '/profile', icon: UserRound, label: 'Profile' },
];

export default function Header() {
  const navigate = useNavigate();

  async function handleLogout() {
    await authApi.logout();
    navigate('/login', { replace: true });
  }

  return (
    <nav className="navbar navbar-expand-lg navbar-light _header_nav _padd_t10 smart-header">
      <div className="container _custom_container">
        <Link className="navbar-brand" to="/feed"><AssetImage name="logo.svg" className="_nav_logo" /></Link>
        <form className="_header_form_grp smart-search" onSubmit={(event) => event.preventDefault()}>
          <Search size={17} className="_header_form_svg" />
          <input className="form-control me-2 _inpt1" type="search" placeholder="Search people, posts, groups" />
        </form>
        <ul className="navbar-nav mb-2 mb-lg-0 _header_nav_list ms-auto _mar_r8 smart-nav-list">
          {navItems.map(({ to, icon: Icon, label }) => (
            <li className="nav-item _header_nav_item" key={to}>
              <NavLink className={({ isActive }) => `nav-link _header_nav_link ${isActive ? '_header_nav_link_active' : ''}`} to={to} aria-label={label}>
                <Icon size={22} />
              </NavLink>
            </li>
          ))}
          <li className="nav-item _header_nav_item"><NavLink className="nav-link _header_nav_link" to="/notifications"><Bell size={22} /><span className="_counting">0</span></NavLink></li>
          <li className="nav-item _header_nav_item"><NavLink className="nav-link _header_nav_link" to="/settings"><Settings size={22} /></NavLink></li>
          <li className="nav-item _header_nav_item">
            <button type="button" className="nav-link _header_nav_link btn btn-link" aria-label="Logout" onClick={handleLogout}>
              <LogOut size={22} />
            </button>
          </li>
        </ul>
      </div>
    </nav>
  );
}
