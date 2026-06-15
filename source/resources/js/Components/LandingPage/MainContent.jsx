export default function MainContent() {
  return (
    <>
      <section class="py-20">
        <div class="container mx-auto flex flex-col items-center gap-10 px-4 text-center md:flex-row md:text-left lg:gap-0">

          <div class="min-w-0 flex-1 md:min-w-[450px]">
            <h1 class="mb-5 text-[2.2rem] text-[#0C5EC1] font-semibold leading-[1.1] md:text-[2.8rem] lg:text-[3.2rem]">
              Your Story, Your Way <br class="hidden lg:block" /> Welcome to Buddy Script
            </h1>

          </div>

          <div class="flex-1">
            <img src="images/about-graphic.png" alt="Woman shopping on a large smartphone" />
          </div>

        </div>
      </section>

      <section class="relative overflow-hidden bg-white py-24" id="about">
        <div class="absolute -right-[100px] -top-[25px] z-0 h-[350px] w-[350px] rounded-full bg-[#d6e4ff]/40"></div>

        <div class="container relative z-10 mx-auto flex flex-col-reverse items-center gap-10 px-4 text-center md:flex-row md:text-left lg:gap-20">

          <div class="flex flex-1 items-center justify-center">
            <img src="images/marketing-graphic.png" alt="Smartphone displaying an online store" class="h-auto max-w-[80%]" />
          </div>

          <div class="flex-1 text-justify">
            <h2 class="mb-5 text-[2.2rem] font-semibold text-gray-800 lg:text-[2.8rem]">
              About Us
            </h2>
            <p class="mb-4 text-gray-600">
              <strong>Buddy Script</strong> is the social network where your narrative takes center stage. It is built to help you connect, collaborate, and share your unique life story with a community that gets you. Whether you are building an audience, networking, or simply finding your everyday crew, we provide the tools to script your story.
            </p>
            <p class="mb-4 text-gray-600">
              <div><strong>Our Core Features</strong></div>
              <div>At Buddy Script, you are in control of your narrative. We offer a clean, intuitive platform designed to help you organize and present your life:
                <ul>
                  <li><strong>Chapter Updates: </strong> Share major milestones, daily logs, and everything in between using our categorized "Chapter" timeline.</li>
                  <li><strong>Customizable Profiles: </strong> Create a profile that reflects your unique style and story with customizable themes, layouts, and privacy settings.</li>
                  <li><strong>Community Engagement: </strong> Connect with friends, family, and like-minded individuals through comments, reactions, and private messaging.</li>
                  <li><strong>Story Highlights: </strong> Curate your best moments into "Highlights" that can be shared on your profile or with specific groups.</li>
                </ul>
              </div>
            </p>
            <p class="mb-4 text-gray-600">
              <strong>Our mission is simple:</strong> to empower meaningful connections. We want to give every user the perfect platform to share their story exactly as they want it to be told. We focus on community growth, user privacy, and fostering an environment where real conversations thrive.
            </p>
            <p class="text-gray-600">
              <div>Join us in building the next generation of social networking. If you are ready to collaborate on building a vibrant online community, let’s get started.</div>
            </p>
          </div>

        </div>
      </section>
    </>
  );
}
