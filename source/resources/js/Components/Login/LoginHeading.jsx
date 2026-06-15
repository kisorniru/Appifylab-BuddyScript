import { Link } from "@inertiajs/react";

export default function LoginHeading() {
  return (
    <section className="flex h-[150px] items-center bg-[#D6E4FF]">
      <div className="container mx-auto flex w-full items-center justify-start gap-[15px] px-4">
        <img src="images/login-icon.svg" alt="Lock icon" className="h-[50px] w-[50px]" />
        <Link href="/login" className="text-3xl font-semibold text-[#333] transition-colors duration-300 ease-in-out hover:text-[#0C5EC1]">
          Admin Login
        </Link>
      </div>
    </section>
  );
}