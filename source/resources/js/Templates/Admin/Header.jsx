import { usePage } from '@inertiajs/react';

export default function Header({ title }) {
  const { props } = usePage();
  const { auth } = props;

  return (
    <div className="header flex bg-white shadow-md items-center justify-between mb-4 p-4">
      <h1 className="text-base font-bold text-gray-800">{ title }</h1>
      <div className="flex items-center">
        <span className="text-gray-600 mr-3 text-xs">{ auth.name }</span>
        <img className="w-10 h-10 rounded-full" src={auth.image} alt="User avatar" />
      </div>
    </div>
  )
}
