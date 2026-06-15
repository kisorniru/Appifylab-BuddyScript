import Modal from "../Common/Modal";

export default function StatusChangeConfirmation({ status, isOpenModel, setIsOpenModel, handleYes }) {
  return (
    <Modal title="Confirmation" isOpenModel={isOpenModel} setIsOpenModel={setIsOpenModel} handleYes={handleYes}>
      <div className="w-full text-center">
        Do you want to {status} the item?
      </div>
    </Modal>
  )
}
