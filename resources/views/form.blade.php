<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submit Ticket</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter&display=swap"
    rel="stylesheet"
  />
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-[#1D7AF3] min-h-screen flex flex-col">
  <!-- Top bar -->
  <header class="bg-black text-white flex items-center justify-between px-6 py-2">
    <div class="text-sm">Submit Ticket</div>
    <button
      class="bg-[#0B2E6E] text-white text-sm font-normal rounded-md px-4 py-1"
      type="button"
    >
      Login
    </button>
  </header>

  <!-- White container with form -->
  <main class="flex-grow flex justify-center items-start pt-10 px-6">
    <section
      class="bg-white rounded-xl w-full max-w-4xl p-8"
      style="min-width: 320px;"
    >
      <h2 class="font-bold text-black text-lg mb-6">Create Ticket</h2>
      <form class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:space-x-6 space-y-4 sm:space-y-0">
          <div class="flex-1">
            <label
              for="firstName"
              class="block text-xs text-black mb-1 font-normal"
              >First Name</label
            >
            <input
              id="firstName"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#1D7AF3]"
            />
          </div>
          <div class="flex-1">
            <label
              for="middleName"
              class="block text-xs text-black mb-1 font-normal"
              >Middle Name</label
            >
            <input
              id="middleName"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#1D7AF3]"
            />
          </div>
          <div class="flex-1">
            <label
              for="lastName"
              class="block text-xs text-black mb-1 font-normal"
              >Last Name</label
            >
            <input
              id="lastName"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#1D7AF3]"
            />
          </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:space-x-6 space-y-4 sm:space-y-0">
          <div class="flex-1">
            <label
              for="title"
              class="block text-xs text-black mb-1 font-normal"
              >Title</label
            >
            <input
              id="title"
              type="text"
              class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#1D7AF3]"
            />
          </div>
          <div class="flex-1">
            <label
              for="office"
              class="block text-xs text-black mb-1 font-normal"
              >Office of concern</label
            >
            <select
              id="office"
              class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#1D7AF3]"
            >
              <option></option>
            </select>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:space-x-6 space-y-4 sm:space-y-0">
          <div class="flex-1">
            <label
              for="problemCategory"
              class="block text-xs text-black mb-1 font-normal"
              >Problem Category</label
            >
            <select
              id="problemCategory"
              class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#1D7AF3]"
            >
              <option></option>
            </select>
          </div>
          <div class="flex-1">
            <label
              for="priority"
              class="block text-xs text-black mb-1 font-normal"
              >Priority</label
            >
            <select
              id="priority"
              class="w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-[#1D7AF3]"
            >
              <option></option>
            </select>
          </div>
        </div>

        <div>
          <label
            for="attachments"
            class="block text-xs text-black mb-1 font-normal"
            >Attachments</label
          >
          <textarea
            id="attachments"
            rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm resize-none"
            placeholder="Drag & Drop your files of "
          ></textarea>
          <div class="text-center text-xs text-black mt-1">
            Drag &amp; Drop your files of
            <span class="underline cursor-pointer">Browse</span>
          </div>
        </div>

        <div class="flex justify-center mt-6">
          <button
            type="submit"
            class="bg-[#0B2E6E] text-white text-sm font-normal rounded-md px-5 py-1"
          >
            Submit Ticket
          </button>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
