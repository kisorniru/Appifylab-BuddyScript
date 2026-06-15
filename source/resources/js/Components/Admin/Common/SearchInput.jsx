export default function SearchInput({ searchText, setSearchText }) {
  return (
    <div className="relative h-10 flex-grow rounded-lg bg-white sm:min-w-[300px] lg:min-w-[400px]">
      <span className="absolute left-3 top-1/2 -translate-y-1/2">
        <img src='/images/admin/search-icon.svg' />
      </span>
      <input
        type="text"
        placeholder="Search..."
        className="h-full w-full rounded-lg bg-transparent pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
        value={searchText}
        onChange={(e) => setSearchText(e.target.value)}
      />
    </div>
  );
}
