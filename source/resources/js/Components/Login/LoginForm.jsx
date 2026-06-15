export default function LoginForm({ data, setData, processing, errors, handleSubmit }) {
  return (
    <section className="flex flex-1 items-center justify-center bg-white py-10">
      <div className="w-96 max-w-sm rounded-lg border border-[#98BAFF] bg-[#F4F5FC] px-[50px] pt-[50px] pb-[120px] shadow-md">
        <h2 className="mb-6 text-center text-xl font-bold text-zinc-800">Admin Login</h2>
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label htmlFor="email" className="mb-2 block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" placeholder="username@buddyscript.us"
              value={data.email}
              className="w-full bg-white appearance-none rounded border border-gray-300 px-3 py-2 leading-tight text-gray-300 shadow-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#0C5EC1]"
              onChange={(e) => setData("email", e.target.value)}
            />
            {errors.email && <p className="mt-2 text-sm text-red-600">{errors.email}</p>}
          </div>
          <div className="mb-6">
            <label htmlFor="password" className="mb-2 block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" placeholder="********"
              className="w-full bg-white appearance-none rounded border border-gray-300 px-3 py-2 leading-tight text-gray-300 shadow-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#0C5EC1]"
              onChange={(e) => setData("password", e.target.value)}
            />
            {errors.password && <p className="mt-2 text-sm text-red-600">{errors.password}</p>}
          </div>
          <div className="flex items-center justify-center">
            <button
              type="submit"
              disabled={processing}
              className={`w-full flex items-center justify-center rounded px-4 py-2 font-bold text-white transition-colors focus:outline-none focus:shadow-outline
                ${processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-[#0D47A1] hover:bg-[#0C5EC1]'}`}
            >
              {processing ? (
                <img src="/images/admin/bouncing-circles.svg" className="w-4 h-[25px]" />
              ) : (
                "Login"
              )}
            </button>
          </div>
        </form>
      </div>
    </section>
  )
}
