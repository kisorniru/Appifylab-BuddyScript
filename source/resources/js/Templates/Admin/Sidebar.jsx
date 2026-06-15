import { Link, useForm, usePage } from "@inertiajs/react";

export default function Sidebar() {
  let isActive;

  const { url } = usePage();
  const { post, processing } = useForm();

  const handleLogout = (e) => {
    e.preventDefault();

    post("/admin/logout");
  }

  const items = [
    { href: "/admin", icon: "/images/admin/home.svg", text: "Dashboard" },
    {
      href: "/admin/users", hrefAlias: ["^/admin/user/.*"],
      icon: "/images/admin/user-multiple-svgrepo-com.svg", text: "Users Management"
    }
  ];

  return (
    <aside className="w-64 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col">
      <div className="h-20 flex items-center justify-center px-6">
        <img src="/images/logo-final.svg" alt="BuddyScript Logo" className="h-8" />
      </div>
      <nav className="flex-1 px-4 py-6">
        {items.map((item, index) => {
          isActive = url.replace(/\?.*/, '') === item.href;

          if (! isActive && item.hrefAlias) {
            isActive = item.hrefAlias.some(pattern => {
              const regex = new RegExp(pattern);
              return regex.test(url);
            });
          }

          return (
            <Link
              key={`sidebar-items-${item.href}`}
              href={item.href}
              className={`
                flex items-center px-4 py-2.5 text-xs relative
                ${index < items.length - 1 ? '[border-bottom:1px_solid_#727C8E1A]' : ''}
                ${isActive
                  ? 'text-gray-700 border-l-4 border-[#1971CE] font-semibold'
                  : 'text-gray-600 hover:bg-gray-100'
                }
              `}
            >
              <img src={item.icon} alt="" className="w-5 h-5 mr-3" />
              <span>{item.text}</span>
            </Link>
          );
        })}
      </nav>

      <div className="p-4 mb-16">
        <button
          className="w-full flex item-center justify-center py-2 text-cyan-600 border border-cyan-400 rounded-full font-semibold hover:bg-cyan-50 transition-colors text-xs"
          disabled={processing}
          onClick={handleLogout}
        >
          {processing ? (
            <img src="/images/admin/bouncing-circles-logout.svg" className="w-4 h-[16px]" />
          ) : (
            "LOG OUT"
          )}
        </button>
      </div>
    </aside>
  );
}
