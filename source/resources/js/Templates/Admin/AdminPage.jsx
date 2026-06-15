import { Head } from "@inertiajs/react";
import Footer from "./Footer";
import Header from "./Header";
import Sidebar from "./Sidebar";
import Breadcrumb from "./Breadcrumb";

export default function AdminPage({ children, title, hasBreadcrumb = true }) {
  return (
    <div className="h-screen w-full bg-white flex font-sans">
      <Head title={title} />
      <Sidebar />
      <div className="flex-1 flex flex-col h-screen overflow-hidden">
        <main className="flex-1 overflow-y-auto bg-gray-50 p-8 pb-20">
          <Header title={title} />
          {!! hasBreadcrumb && (<Breadcrumb />)}
          {children}
        </main>
        <Footer />
      </div>
    </div>
  )
}