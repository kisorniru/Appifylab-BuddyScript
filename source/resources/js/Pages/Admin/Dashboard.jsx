import DashboardCards from '../../Components/Admin/Sections/DashboardCards';
import AdminPage from '../../Templates/Admin/AdminPage';
import ProgressBarChart from '../../Components/Admin/Sections/ProgressBarChart';

export default function Dashboard({ cards, users }) {
  return (
    <AdminPage title="Dashboard" hasBreadcrumb={false}>
      <div className="flex-1 bg-white p-8 mt-6 shadow-lg text-xs">
        <DashboardCards data={cards} />

        <div className='flex flex-col lg:flex-row lg:gap-8 space-y-8 lg:space-y-0 mt-6'>
          <ProgressBarChart title="User Report" chartData={users} />
        </div>
      </div>
    </AdminPage>
  );
}
