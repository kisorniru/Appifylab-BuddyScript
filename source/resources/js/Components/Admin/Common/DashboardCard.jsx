export default function DashboardCard({ title, value, icon, color }) {
  return (
    <div className="bg-white p-5 rounded-xl flex items-center space-x-4 transition-all duration-300 border border-[#EAEAEA]">
      {/* Icon Container */}
      <div className={`flex-shrink-0 p-2.5 rounded-full`}>
        <img src={`/images/admin/dashboard/icon-${icon}.svg`} className="w-10" />
      </div>
      {/* Stats Text */}
      <div>
        <p className={`text-2xl font-semibold`} style={{ color: color }}>{value}</p>
        <p className="text-sm text-gray-600 font-regular">{title}</p>
      </div>
    </div>
  );
}
