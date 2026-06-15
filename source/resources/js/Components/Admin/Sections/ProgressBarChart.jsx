export default function ProgressBarChart({ title, chartData }) {
  const defaultData = {
    today: { label: 'Today', color: 'bg-[#A3A0FB]', ringColor: 'ring-[#A3A0FB]' },
    week: { label: 'This week', color: 'bg-[#147CC1]', ringColor: 'ring-[#147CC1]' },
    month: { label: 'This Month', color: 'bg-[#CEC80D]', ringColor: 'ring-[#CEC80D]' },
    year: { label: 'This Year', color: 'bg-[#2EC114]', ringColor: 'ring-[#2EC114]' },
  };

  // Find the maximum value to scale the bars correctly.
  const maxValue = Math.max(...chartData.map(item => item.value), 0);

  return (
    <div className="w-full mx-auto bg-white border border-[#EAEAEA] p-6">
      <h2 className="text-xl font-semibold text-gray-800 mb-6">{title}</h2>

      <div className="space-y-5 px-10 py-12">
        {chartData.map((item) => {
          // Calculate the width of the bar as a percentage of the max value.
          const barWidth = maxValue > 0 ? `${(item.value / maxValue) * 100}%` : '0%';

          return (
            <div key={`progress-bar-chart-${item.key}`} className="grid grid-cols-12 items-center gap-4 text-sm">

              {/* Label and Ring */}
              <div className="col-span-3 flex items-center">
                <div className={`w-4 h-4 rounded-full ring-2 ${defaultData[item.key].ringColor} mr-3`}></div>
                <span className="text-gray-600">{defaultData[item.key].label}</span>
              </div>

              {/* Progress Bar */}
              <div className="col-span-6">
                <div className="w-full rounded-full h-1.5">
                  <div
                    className={`${defaultData[item.key].color} h-1.5 rounded-full transition-all duration-500`}
                    style={{ width: barWidth }}
                  ></div>
                </div>
              </div>

              {/* Value */}
              <div className="col-span-3 text-right">
                <span className="font-semibold text-gray-800">
                  {item.value.toLocaleString()}
                </span>
              </div>

            </div>
          );
        })}
      </div>
    </div>
  );
}
