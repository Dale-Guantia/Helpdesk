<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Ticket - Help Desk</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
  <style>
    /* Custom scrollbar for textarea */
    textarea::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    textarea::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 3px;
    }
  </style>
</head>
<body class="bg-[#f5f5f5] font-sans text-gray-900">
  <!-- Header -->
  <header class="bg-[#0e2f66] flex justify-between items-center px-6 py-3">
    <span class="text-white font-extrabold text-lg select-none">HELP DESK</span>
    <a class="bg-[#118bf0] text-white text-sm rounded-md px-4 py-2 font-sans" type="button" href="{{ route('filament.ticketing.auth.login')}}">
        Login
    </a>
  </header>

  <main class="px-4 sm:px-6 md:px-8 py-6 max-w-7xl mx-auto flex justify-center">
    <form class="bg-white rounded-md shadow-sm max-w-5xl w-full">
      <h1 class="font-semibold text-lg mb-6 select-none">Create Ticket</h1>

      <fieldset class="border border-gray-200 rounded-md p-4">
        <legend class="text-xs font-semibold px-2 select-none">Ticket Details</legend>
        <div class="flex flex-col md:flex-row gap-6">
          <!-- Left side inputs -->
          <div class="flex-1 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label for="first-name" class="block text-xs font-semibold mb-1 select-none">First name</label>
                <input
                  id="first-name"
                  name="first-name"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                  autocomplete="off"
                />
              </div>
              <div>
                <label for="middle-name" class="block text-xs font-semibold mb-1 select-none">Middle name</label>
                <input
                  id="middle-name"
                  name="middle-name"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                  autocomplete="off"
                />
              </div>
              <div>
                <label for="last-name" class="block text-xs font-semibold mb-1 select-none">Last name</label>
                <input
                  id="last-name"
                  name="last-name"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                  autocomplete="off"
                />
              </div>
            </div>

            <div>
              <label for="title" class="block text-xs font-semibold mb-1 select-none">Title<span class="text-red-600">*</span></label>
              <input
                id="title"
                name="title"
                type="text"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                autocomplete="off"
              />
            </div>
            <div>
              <label for="message" class="block text-xs font-semibold mb-1 select-none">Message</label>
              <textarea
                id="message"
                name="message"
                rows="3"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 resize-y focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
              ></textarea>
            </div>
            <div>
              <label for="attachment" class="block text-xs font-semibold mb-1 select-none">Attachment</label>
              <div
                id="attachment"
                class="w-full border border-gray-300 rounded-md px-3 py-8 text-center text-xs text-gray-500 cursor-pointer select-none"
              >
                Drag &amp; Drop your files or <a href="#" class="text-blue-600 hover:underline">Browse</a>
              </div>
            </div>
          </div>

          <!-- Right side inputs -->
          <div class="w-full md:w-64 space-y-4">
            <div>
              <label for="office" class="block text-xs font-semibold mb-1 select-none">Office of concern</label>
              <select
                id="office"
                name="office"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
              >
                <option selected disabled>Select an option</option>
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
              </select>
            </div>
            <div>
              <label for="problem-category" class="block text-xs font-semibold mb-1 select-none">Problem Category</label>
              <select
                id="problem-category"
                name="problem-category"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
              >
                <option disabled selected></option>
                <option>Category 1</option>
                <option>Category 2</option>
                <option>Category 3</option>
              </select>
            </div>
            <div>
              <label for="priority" class="block text-xs font-semibold mb-1 select-none">Priority Level</label>
              <select
                id="priority"
                name="priority"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
              >
                <option selected>Low</option>
                <option>Medium</option>
                <option>High</option>
              </select>
            </div>
          </div>
        </div>
      </fieldset>

      <div class="mt-4 flex space-x-2 px-2 pb-4">
        <button
          type="submit"
          class="bg-[#118bf0] text-white text-xs font-semibold rounded px-3 py-1.5 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600"
        >
          Create
        </button>
        <a
            class="bg-white border border-gray-300 text-xs font-normal rounded px-3 py-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600"
            href="{{ route('index') }}"
        >
            Cancel
        </a>
      </div>
    </form>
  </main>
</body>
</html>
