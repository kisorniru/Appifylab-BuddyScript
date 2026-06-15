import SearchInput from '../Common/SearchInput';

export default function SearchUserStatusFilter({ searchText, setSearchText,}) {

  return (
    <div className="flex w-full flex-col gap-4 rounded-lg sm:flex-row mb-5 sm:items-center sm:justify-between">

      {/* Search Input Field */}
      <SearchInput searchText={searchText} setSearchText={setSearchText} />
    </div>
  );
}
