import React, { useState } from 'react';
import StatusChangeConfirmation from '../Modals/StatusChangeConfirmation';

/**
 * A reusable status switch component, using the provided JSX.
 *
 * @param {object} props - The component props.
 * @param {string|number} props.id - A unique ID for the toggle.
 * @param {number} props.status - The current status (e.g., 1 for active, 0 for inactive).
 * @param {boolean} props.isProcessing - If true, disables the switch and shows a loader.
 * @param {function} props.changeStatus - The function to call when the switch is toggled.
 * @param {function} props.selectedStatus - The changed status (e.g., 1 for active, 0 for inactive).
 * @param {function} props.setSelectedStatus - The function to call to bind changed status.
 * @param {function} props.getStatus - A function that takes the status and returns a display string.
 */
export default function Switch({ id, status, isProcessing, changeStatus, selectedStatus, setSelectedStatus, getStatus }) {
  const [isOpenModel, setIsOpenModel] = useState(false);

  const onStatusChange = (e) => {
    setIsOpenModel(true);
    setSelectedStatus(e.target.checked);
  };

  const handleYes = () => {
    setIsOpenModel(false);
    changeStatus(id);
  }

  return (
    <>
      <div className="flex items-center space-x-2">
        <span className={`text-xs font-medium text-slate-500`}>
          {isProcessing ? (
            <img
              src="/images/admin/bouncing-circles-logout.svg"
              className="w-4 h-[16px]"
              alt="Processing..."
            />
          ) : (
            getStatus(status)
          )}
        </span>

        <label
          htmlFor={`toggle-${id}`}
          className="relative inline-flex h-5 w-9 cursor-pointer items-center"
        >
          <input
            type="checkbox"
            id={`toggle-${id}`}
            className="peer sr-only"
            checked={status === 1}
            onChange={onStatusChange}
            disabled={isProcessing}
          />
          <div className="h-full w-full rounded-full border-2 bg-transparent transition-colors peer-checked:border-green-500 border-slate-300"></div>
          <div className="absolute left-1 top-1 h-3 w-3 rounded-full bg-slate-400 transition-transform peer-checked:translate-x-4 peer-checked:bg-green-500"></div>
        </label>
      </div>

      <StatusChangeConfirmation
        status={getStatus(selectedStatus).toLowerCase()}
        isOpenModel={isOpenModel}
        setIsOpenModel={setIsOpenModel}
        handleYes={handleYes}
      />
    </>
  );
}
