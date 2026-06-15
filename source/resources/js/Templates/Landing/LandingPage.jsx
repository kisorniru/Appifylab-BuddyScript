import Header from './Header';
import Footer from './Footer';
import { Head } from '@inertiajs/react';

export default function LandingPage({ children, title }) {
  return (
    <div className="flex min-h-screen flex-col">
      <Head title={title} />
      <Header />
      <main className="flex flex-1 flex-col">
        {children}
      </main>
      <Footer />
    </div>
  );
}
