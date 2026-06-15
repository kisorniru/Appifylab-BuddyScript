import React, { useState } from 'react';
import Button from './Button'

export default function Modal({
  title="Modal",
  children,
  isOpenModel,
  setIsOpenModel,
  showYesButton = true,
  yesButtonText = "Yes",
  cancelButtonText = "Cancel",
  processing,
  handleYes = () => { },
  handleCancel = () => { }
}) {
  if (!isOpenModel) {
    return;
  }

  const handleClose = () => {
    setIsOpenModel(false);
    handleCancel();
  }

  return (
    <div className="fixed inset-0 z-100 flex items-center justify-center font-sans p-4">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto">
        <div className="flex justify-between items-center py-1 px-5 bg-[#EBF2F2] rounded-t-xl px-2">
          <h2 className="text-sm font-semibold text-gray-800">{title}</h2>
          <button
            onClick={handleClose}
            className="text-gray-400 hover:text-gray-600 transition-colors duration-300"
            aria-label="Close"
          >
            <img src='/images/admin/modal-close.svg' className='w-10 h-10 cursor-pointer' />
          </button>
        </div>

        <div className="p-18">
          <div className="mb-12">
            {children}
          </div>

          <div className="flex justify-center items-center space-x-2">
            {showYesButton && <Button click={handleYes} type='primary' processing={processing}>{yesButtonText}</Button>}
            <Button click={handleClose}>{cancelButtonText}</Button>
          </div>
        </div>
      </div>
    </div>
  );
};
