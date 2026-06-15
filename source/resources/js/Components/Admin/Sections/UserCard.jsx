import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import Switch from '../Common/Switch';

export default function UserCard({ user, isDisabled = false }) {
  const { id, name, email, imageUrl, status, isDeleted, deletedAt } = user;
  const [isProcessing, setIsProcessing] = useState(false);
  const [selectedStatus, setSelectedStatus] = useState(false);

  const getStatus = (status) => {
    return status ? "Active" : "Inactive";
  }

  const changeStatus = () => {
    router.patch(
      `/admin/user/${id}/active-status`,
      { status: selectedStatus },
      {
        preserveScroll: true,
        onStart: () => setIsProcessing(true),
        onFinish: () => setIsProcessing(false),
      }
    );
  }

  return (
    <div className="w-full rounded-lg border border-slate-200 bg-white p-4 font-sans shadow-sm">
      {/* Top section */}
      <div className="flex items-center justify-between">
        <div className="flex items-center">
          <img
            src={imageUrl}
            alt={`Avatar of ${name}`}
            className="h-14 w-14 rounded-full object-cover"
          />
          <div className="ml-4">
            <p className="text-sm font-bold text-slate-800">{name}</p>
            <p className="text-xs text-slate-500">{email}</p>
            {!! isDeleted && (<i className="text-[11px] text-slate-500">{deletedAt}</i>)}
          </div>
        </div>

        {/* Toggle */}
        {! isDeleted && (<Switch
          id={id}
          status={status}
          isProcessing={isProcessing}
          selectedStatus={selectedStatus}
          setSelectedStatus={setSelectedStatus}
          changeStatus={changeStatus}
          getStatus={getStatus}
        />)}
      </div>
    </div>
  );
};
