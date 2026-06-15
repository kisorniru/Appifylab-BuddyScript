export default function Button({ click, type = "default", processing, className, children }) {
  const allButtonTypes = {
    primary: "bg-blue-600 text-white border-blue-700 hover:bg-blue-700 focus:ring-blue-500",
    danger: "bg-red-600 text-white border-red-700 hover:bg-red-700 focus:ring-red-500",
    success: "bg-green-600 text-white border-green-700 hover:bg-green-700 focus:ring-green-500",
    raw: "bg-white text-blue-700 border-blue-700 hover:bg-gray-100 focus:ring-gray-300",
    default: "bg-white text-gray-700 border-gray-300 hover:bg-gray-100 focus:ring-gray-300"
  };

  let typeClasses = allButtonTypes[type] || allButtonTypes.default;
  const buttonContent = typeof children === 'string' ? children.toUpperCase() : children;

  if (className !== undefined) {
    typeClasses = `${typeClasses} ${className}`;
  }

  return (
    <button
      onClick={click}
      className={`${typeClasses} relative flex justify-center items-center font-semibold text-xs py-2 px-8 rounded-sm border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-300 shadow-sm hover:shadow-lg`}
      disabled={typeClasses !== "default" && processing}
    >
      {(typeClasses !== "default" && processing) && (
        <div className="absolute">
          <img src={`/images/admin/bouncing-circles${type.match(/raw$/gi) ? '-logout' : ''}.svg`} className="w-4 h-4" alt="Loading..." />
        </div>
      )}

      <span className={(typeClasses !== "default" && processing) ? 'invisible' : ''}>
        {buttonContent}
      </span>
    </button>
  );
}
