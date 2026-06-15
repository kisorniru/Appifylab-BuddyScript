import { Link } from "@inertiajs/react";

export default function Header() {
  return (
    <header className="border-b border-gray-200 bg-white py-4">
      <div className="container mx-auto flex flex-col items-center gap-5 px-4 md:flex-row md:gap-0">

        <div className="logo">
          <img src="images/logo-final.svg" alt="BuddyScript Logo" className="h-10" />
        </div>

        <nav className="md:ml-auto md:mr-10">
          <ul className="flex gap-6">
            <li>
              <Link href="/#about" className="font-semibold text-gray-800 transition-colors duration-300 hover:text-[#0C5EC1]">About Us</Link>
            </li>
          </ul>
        </nav>

        {/* <Link href="/login" className="whitespace-nowrap rounded-full bg-[#0C5EC1] px-[35px] py-2.5 font-bold text-white transition-colors duration-300 hover:bg-[#0a4a9c]">
          Login
        </Link> */}
      </div>
    </header>
  );
}
