import { router } from "@inertiajs/react";
import { useState } from "react";
import { updateQueryParams } from "../../../utility/Helper";

export default function DefaultPagination({ data }) {
  let label, isDisabled, isActive;

  const links = data.links;
  const totalItems = data.total;
  const [itemsPerPage, setItemsPerPage] = useState(data.per_page);

  const onItemsPerPageChange = (page) => {
    setItemsPerPage(page);

    updateQueryParams({
      offset: page,
      page: 1
    })
  }

  const decodeHtml = (html) => {
    const txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
  }

  const onPageChange = (page) => {
    if (! page) {
      return;
    }

    updateQueryParams({
      offset: itemsPerPage,
      page: page
    })
  }

  return (
    <div className="flex w-full flex-col items-center justify-between gap-4 rounded-lg p-4 sm:flex-row">
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
        {links.map((link, index) => {
          label = decodeHtml(link.label);
          isDisabled = !link.url;
          isActive = link.active;

          if (label.toLowerCase().includes("previous")) {
            return (
              <button
                key={`prev-${index}`}
                onClick={() => onPageChange(link.page)}
                disabled={isDisabled}
                className="flex h-9 w-9 items-center justify-center rounded-md cursor-pointer border border-slate-300 bg-white text-slate-500 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
              >
                <img src="/images/admin/right-icon.svg" className="w-4 h-4 rotate-180" />
              </button>
            );
          }

          if (label.toLowerCase().includes("next")) {
            return (
              <button
                key={`next-${index}`}
                onClick={() => onPageChange(link.page)}
                disabled={isDisabled}
                className="flex h-9 w-9 items-center justify-center rounded-md cursor-pointer border border-slate-300 bg-white text-slate-500 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
              >
                <img src="/images/admin/right-icon.svg" className="w-4 h-4" />
              </button>
            );
          }

          return (
            <button
              key={`page-${index}`}
              onClick={() => (data.current_page != link.page) && onPageChange(link.page)}
              disabled={isDisabled}
              className={`flex h-9 w-9 items-center justify-center rounded-md border text-sm font-medium cursor-pointer
              ${isActive
                  ? "border-[#0C5EC1] bg-[#0C5EC1] text-white"
                  : "border-slate-300 bg-white text-slate-700 hover:bg-slate-50"
                }`}
            >
              {label}
            </button>
          );
        })}
      </div>
    </div>
  )
}
