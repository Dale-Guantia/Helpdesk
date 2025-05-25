<!DOCTYPE html>
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Help Desk Ticketing System
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Russo+One&amp;display=swap" rel="stylesheet"/>
  <style>
   body {
      font-family: 'Russo One', sans-serif;
    }
  </style>
 </head>
 <body class="bg-[#f5f5f5] min-h-screen flex flex-col">
  <header class="bg-[#0e2f66] flex justify-between items-center px-6 py-3">
    <span class="text-white font-extrabold text-lg select-none">HELP DESK</span>
    <a class="bg-[#118bf0] text-white text-sm rounded-md px-4 py-2 font-sans" type="button" href="{{ route('filament.ticketing.auth.login')}}">
        Login
    </a>
  </header>
  <main class="flex-grow flex flex-col justify-center items-center text-black text-center px-4">
    <img alt="logo" class="h-48 w-auto" src="{{ asset('images/PrimaryLogo.png') }}"/>
    <br/>
    <h1 class="text-3xl sm:text-3xl md:text-3xl leading-tight">
        HELP DESK
        <br/>
        TICKETING SYSTEM
    </h1>
   <div class="mt-10 w-full max-w-md flex justify-between px-6">
    <div class="flex flex-col pr-10 items-center">
    <a class="bg-[#118bf0] text-white text-s rounded-md px-4 py-2 font-sans" type="button" href="{{ route('submit_ticket')}}">
        Submit Ticket
    </a>
     {{-- <p class="text-[16px] mt-1 font-sans">
      Questions? We're here to help!
     </p> --}}
    </div>
    <div class="flex flex-col pl-10 items-center">
     <button class="bg-[#118bf0] text-white text-s rounded-md px-4 py-2 font-sans" type="button">
      Track Ticket
     </button>
     {{-- <p class="text-[16px] mt-1 font-sans">
      Tap here to view updates!
     </p> --}}
    </div>
   </div>
  </main>
 </body>
</html>
