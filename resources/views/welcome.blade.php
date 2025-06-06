<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="dark">

<head>
  @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">


  <nav class="bg-white border-gray-200 dark:bg-zinc-900">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
      <a href="/"
        class="flex flex-col space-x-3 rtl:space-x-reverse">
        <span class=" text-2xl font-semibold whitespace-nowrap dark:text-white">Naale MV</span>
        <span class=" text-sx ml-10 whitespace-nowrap dark:text-white">Create Your Own Post</span>
      </a>
      <button data-collapse-toggle="navbar-default"
        type="button"
        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-zinc-700 dark:focus:ring-gray-600"
        aria-controls="navbar-default"
        aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 17 14">
          <path stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M1 1h15M1 7h15M1 13h15" />
        </svg>
      </button>
      <div class="hidden w-full md:block md:w-auto"
        id="navbar-default">


        @if (Route::has('login'))
        <nav class="flex items-center justify-end gap-4">
          @auth
          <a href="{{ url('/dashboard') }}"
            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
            Dashboard
          </a>
          @else
          <a href="{{ route('login') }}"
            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
            Log in
          </a>

          @if (Route::has('register'))
          <a href="{{ route('register') }}"
            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
            Register
          </a>
          @endif
          @endauth
        </nav>
        @endif
      </div>
    </div>
  </nav>

  @if ($errors->any())
  <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4"
    role="alert">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <div id="loadingDisplay"
    class=" fixed ute bg-blue-200/30 hidden flex justify-center left-0 top-0 z-50 w-screen h-screen bg-secondary/40 overflow-hidden">
    <div role="status"
      class=" relative top-2/4  ">
      <svg aria-hidden="true"
        class="w-16 h-16 text-gray-200 animate-spin  fill-primary"
        viewBox="0 0 100 101"
        fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path
          d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
          fill="currentColor" />
        <path
          d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
          fill="currentFill" />
      </svg>
      <span class="sr-only">Loading...</span>
    </div>
  </div>

  <script>
    function isLoading() {
            document.getElementById('loadingDisplay').classList.remove('hidden');
        }
        document.addEventListener('DOMContentLoaded', function () {
                    const loadingScreen = document.getElementById('loading-screen');
                    window.addEventListener('beforeunload', function () {
                        isLoading()
                    });
                });
  </script>
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

    <div class="flex justify-center flex-col md:flex-row gap-4 p-2 h-full overflow-hidden">
      <div class="w-full md:w-52 h-full overflow-hidden ">
        <script async
          src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9427898748394345"
          crossorigin="anonymous"></script>
        <!-- Ad 002 -->
        <ins class="adsbygoogle"
          style="display:block"
          data-ad-client="ca-pub-9427898748394345"
          data-ad-slot="1968872696"
          data-ad-format="auto"
          data-full-width-responsive="true"></ins>
        <script>
          (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div>
      <form method="post"
        action="/create-post-new"
        enctype="multipart/form-data"
        class="w-full md:w-1/2 flex justify-center flex-col mx-auto p-2"
        x-data="{ type: 'with_photo' }">
        @csrf



        <div class="w-full mb-5 "
          x-show="type === 'with_photo'">
          <div class="w-full">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select your
              Image</label>
            @livewire('photo-upload')
          </div>
        </div>
        <div class="mb-5 w-full">
          <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">What the Celebration
            About <span class="text-red-500 text-md">*</span></label>
          <input type="text"
            placeholder="Eg: happy Anniversary Love"
            name="celebration_title"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>

        {{-- <div class="mb-5 w-full">
          <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select the date</label>
          <input type="date"
            name="date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div> --}}

        <div class="mb-5 w-full">
          <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Additional messages
          </label>
          <input type="text"
            placeholder="eg: Here's to another year of love, laughter, and cherished moments together!"
            name="message"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        </div>

        <button type="submit"
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create
          Post</button>
      </form>

      <div class="w-52 h-full overflow-hidden hidden md:block">
        <script async
          src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9427898748394345"
          crossorigin="anonymous"></script>
        <ins class="adsbygoogle"
          style="display:block"
          data-ad-format="autorelaxed"
          data-ad-client="ca-pub-9427898748394345"
          data-ad-slot="1139656380"></ins>
        <script>
          (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div>
    </div>
    <div
      class="relative p-2 h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
      @if ($PostInfor??null)
      @if (!$PostInfor->post_path)
      <h2 class="text-center w-full text-xl p-2">Select A images from Below</h2>

      @endif
      <div class="w-full md:w-1/2 mx-auto">
        @if ($PostInfor->post_path)
        @forelse ($PostInfor->post_path as $imageUrl)
        <div class="overflow-hidden rounded-xl relative group">
          <a href="{{ $imageUrl }}"
            target="_blank">
            <img
              class="hover:ring-blue-500 hover:ring-1 hover:scale-110 transition-transform duration-1000 ease-in-out object-cover w-full h-full"
              src="{{ $imageUrl }}"
              alt="Post Image" />
          </a>
          <div
            class="absolute inset-0 bg-gray-500/40 bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <a href="{{ $imageUrl }}"
              download
              class="p-2 rounded-full bg-white hover:bg-gray-200">
              <svg xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-gray-800"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </a>
          </div>
        </div>

        @empty
        @endforelse

        @else
        @forelse ($PostInfor->category->custom_img_path ?? $PostInfor->custom_img_path as $imageUrl)
        <div class="overflow-hidden rounded-xl relative group">
          <img
            class="hover:ring-blue-500 hover:ring-1 hover:scale-150 transition-transform duration-1000 ease-in-out object-cover w-full h-full cursor-pointer"
            src="{{ $imageUrl.'?auto=compress&cs=tinysrgb&dpr=1&fit=crop&h=200&w=280' }}"
            alt="Post Image"
            onclick="window.location.href='/generate-post/{{$PostInfor->id}}?image_url={{ urlencode($imageUrl) }}'" />
          <div
            class="absolute inset-0 bg-gray-500/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <button
              onclick="window.location.href='/generate-post/{{$PostInfor->id}}?image_url={{ urlencode($imageUrl) }}'"
              class="p-2 rounded-full bg-white hover:bg-gray-200">
              <svg xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-gray-800"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>

        @empty

        @endforelse
        @endif
      </div>

      @else

      <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
      @endif
    </div>
  </div>
  @fluxScripts
  <div class="max-w-screen-xl mx-auto p-4">
    <script async
      src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9427898748394345"
      crossorigin="anonymous"></script>
    <ins class="adsbygoogle"
      style="display:block; text-align:center;"
      data-ad-layout="in-article"
      data-ad-format="fluid"
      data-ad-client="ca-pub-9427898748394345"
      data-ad-slot="4307911051"></ins>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
  </div>
</body>

</html>