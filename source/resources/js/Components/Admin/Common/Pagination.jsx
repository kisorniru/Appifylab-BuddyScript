export default function Pagination({
  currentPage,
  totalItems,
  itemsPerPage,
  onPageChange,
  onItemsPerPageChange,
}) {
  const generatePageNumbers = (currentPage, totalPages, windowSize = 5) => {
    if (totalPages <= windowSize) {
      return Array.from({ length: totalPages }, (_, i) => i + 1);
    }

    const halfWindow = Math.floor(windowSize / 2);
    let startPage = currentPage - halfWindow;

    startPage = Math.max(1, startPage);
    startPage = Math.min(startPage, totalPages - windowSize + 1);

    const pageNumbers = Array.from({ length: windowSize }, (_, i) => startPage + i);
    const pagesWithEllipses = [];

    if (startPage > 1) {
      pagesWithEllipses.push('...');
    }

    pagesWithEllipses.push(...pageNumbers);

    if (startPage + windowSize - 1 < totalPages) {
      pagesWithEllipses.push('...');
    }

    return pagesWithEllipses;
  };

  const totalPages = Math.ceil(totalItems / itemsPerPage);
  const pageNumbers = generatePageNumbers(currentPage, totalPages, 5);

  const handlePrevious = () => {
    if (currentPage > 1) {
      onPageChange(currentPage - 1);
    }
  };

  const handleNext = () => {
    if (currentPage < totalPages) {
      onPageChange(currentPage + 1);
    }
  };

  return (
    <div className="flex w-full flex-col items-center justify-between gap-4 rounded-lg p-4 mt-4 sm:flex-row">
      <div className="flex items-center gap-2">
        <span className="text-sm text-slate-700">
          Total <span className="font-bold">{totalItems}</span>
        </span>
        <span className="ml-4 text-sm text-slate-700">Row</span>
        <div className="relative inline-block">
          <select
            value={itemsPerPage}
            onChange={(e) => onItemsPerPageChange(Number(e.target.value))}
            className="h-9 w-32 appearance-none rounded-md border border-slate-300 bg-white pl-3 pr-6 text-sm text-slate-700 shadow-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
          >
            <option value={10}>10</option>
            <option value={20}>20</option>
            <option value={25}>25</option>
            <option value={50}>50</option>
          </select>

          <img
            src="/images/admin/page-count-dropdown.svg"
            className="pointer-events-none absolute right-3 top-1/2 h-4 w-3 -translate-y-1/2"
            alt="Dropdown arrow"
          />
        </div>
      </div>

      {/* Right side: Pagination controls */}
      <div className="flex items-center space-x-2">
        <button
          onClick={handlePrevious}
          disabled={currentPage === 1}
          className="flex h-9 w-9 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-500 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <img src="/images/admin/right-icon.svg" className="w-4 h-4 rotate-180" />
        </button>

        {pageNumbers.map((page, index) => {
          if (typeof page === 'string') {
            return (
              <span key={`ellipsis-${index}`} className="flex h-9 w-9 items-center justify-center px-3 text-sm text-slate-500">
                {page}
              </span>
            );
          }
          return (
            <button
              key={page}
              onClick={() => onPageChange(page)}
              className={`flex h-9 w-9 items-center justify-center rounded-md border text-sm font-medium
                ${currentPage === page ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'}`}
            >
              {page}
            </button>
          );
        })}

        <button
          onClick={handleNext}
          disabled={currentPage === totalPages}
          className="flex h-9 w-9 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-500 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <img src="/images/admin/right-icon.svg" className="w-4 h-4" />
        </button>
      </div>
    </div>
  );
}
