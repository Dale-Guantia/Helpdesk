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
 <body class="bg-[#0F7AE5] min-h-screen flex flex-col">
  <header class="bg-white flex justify-between items-center px-6 py-3">
   <img alt="Pasig city government logo with text" class="h-10 w-auto" height="40" src="{{ asset('images/PrimaryLogo.png') }}" width="150"/>
   <a class="bg-[#0B2F5A] text-white text-sm rounded-md px-4 py-2 font-sans" type="button" href="{{ route('filament.ticketing.auth.login')}}">
    Login
   </a>
  </header>
  <main class="flex-grow flex flex-col justify-center items-center text-white text-center px-4">
   <h1 class="text-4xl sm:text-5xl md:text-6xl leading-tight">
    HELP DESK
    <br/>
    TICKETING SYSTEM
   </h1>
   <div class="mt-10 w-full max-w-md flex justify-between px-6">
    <div class="flex flex-col items-center">
    <a class="bg-[#0B2F5A] text-white text-xs rounded-md px-4 py-2 font-sans" type="button" href="{{ route('submit_ticket')}}">
        Submit Ticket
    </a>
     <p class="text-[9px] mt-1 font-sans">
      Questions? We're here to help!
     </p>
    </div>
    <div class="flex flex-col items-center">
     <button class="bg-[#0B2F5A] text-white text-xs rounded-md px-4 py-2 font-sans" type="button">
      Track Ticket
     </button>
     <p class="text-[9px] mt-1 font-sans">
      Tap here to view updates!
     </p>
    </div>
   </div>
  </main>
 </body>
</html>
