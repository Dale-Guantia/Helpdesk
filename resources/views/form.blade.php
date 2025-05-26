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
    <form action="{{ route('ticket_store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-md shadow-sm max-w-5xl w-full">
      @csrf
      <h1 class="font-semibold text-lg mb-6 select-none">Create Ticket</h1>

      <fieldset class="border border-gray-200 rounded-md p-4">
        <legend class="text-xs font-semibold px-2 select-none">Ticket Details</legend>
        <div class="flex flex-col md:flex-row gap-6">
          <!-- Left side inputs -->
          <div class="flex-1 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label for="guest_firstName" class="block text-xs font-semibold mb-1 select-none">First name</label>
                <input
                  id="guest_firstName"
                  name="guest_firstName"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                  autocomplete="off"
                />
              </div>
              <div>
                <label for="guest_middleName" class="block text-xs font-semibold mb-1 select-none">Middle name</label>
                <input
                  id="guest_middleName"
                  name="guest_middleName"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                  autocomplete="off"
                />
              </div>
              <div>
                <label for="guest_lastName" class="block text-xs font-semibold mb-1 select-none">Last name</label>
                <input
                  id="guest_lastName"
                  name="guest_lastName"
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
              <label for="description" class="block text-xs font-semibold mb-1 select-none">Message</label>
              <textarea
                id="description"
                name="description"
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
                <!-- Office Dropdown -->
                <div>
                    <label for="office_id" class="block text-xs font-semibold mb-1">Office of concern</label>
                    <select
                        id="office_id"
                        name="office_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                        required
                    >
                        <option selected>Select an office</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Problem Category Dropdown -->
                <div>
                    <label for="problem_category_id" class="block text-xs font-semibold mb-1">Problem Category</label>
                    <select
                        id="problem_category_id"
                        name="problem_category_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700"
                        required
                        disabled
                    >
                        <option selected disabled>Select an office first</option>
                    </select>
                </div>
                <div>
                    <label for="priority" class="block text-xs font-semibold mb-1 select-none">Priority Level</label>
                        <select
                            id="priority_id"
                            name="priority_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                            required
                        >
                            <option selected>Select an option</option>
                            @foreach ($priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->priority_name }}</option>
                            @endforeach
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
            Submit
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#office_id').on('change', function () {
        var officeId = $(this).val();

        if (!officeId) return;

        // Disable and clear problem categories until fetched
        $('#problem_category_id').prop('disabled', true).html('<option>Loading...</option>');

        $.ajax({
            url: '/problem_categories/' + officeId,
            type: 'GET',
            success: function (data) {
                let options = '<option selected >Select a problem category</option>';
                data.forEach(function (category) {
                    options += `<option value="${problem_category.id}">${problem_category.category_name}</option>`;
                });
                $('#problem_category_id').html(options).prop('disabled', false);
            },
            error: function () {
                $('#problem_category_id').html('<option>Error loading categories</option>').prop('disabled', true);
            }
        });
    });
</script>
</html>
