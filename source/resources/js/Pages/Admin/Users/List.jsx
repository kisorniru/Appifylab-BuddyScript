import { useEffect, useState } from "react";
import DefaultPagination from "../../../Components/Admin/Common/DefaultPagination";
import NoData from "../../../Components/Admin/Common/NoData";
import SearchUserStatusFilter from "../../../Components/Admin/Filters/SearchUserStatusFilter";
import UserCard from "../../../Components/Admin/Sections/UserCard";
import AdminPage from "../../../Templates/Admin/AdminPage";
import { updateQueryParams } from "../../../utility/Helper";

export default function UsersList({ users }) {
  const [searchText, setSearchText] = useState("");

  useEffect(() => {
    updateQueryParams({
      search: searchText
    });
  }, [searchText]);

  return (
    <AdminPage title="Users">
      <div className="min-h-screen">
        <div className="mx-auto">
          <SearchUserStatusFilter
            searchText={searchText}
            setSearchText={setSearchText}
          />

          {users.data.length < 1
            ? <NoData />
            : <>
              <div className="grid grid-cols-1 gap-6 lg:grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 mb-2">
                {users.data.map((user) => (
                  <UserCard key={user.id} user={user} isDisabled={! user.hasStore} />
                ))}
              </div>

              <DefaultPagination data={users} />
            </>
          }
        </div>
      </div>
    </AdminPage>
  );
}
