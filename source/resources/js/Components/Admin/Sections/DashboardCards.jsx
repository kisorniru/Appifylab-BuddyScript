import DashboardCard from "../Common/DashboardCard";

export default function DashboardCards({ data }) {
  return (
    <div className="w-full max-w-8xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <DashboardCard title="Total Users" value={data.users} icon="users" color="#166CCB" />
    </div>
  );
}
