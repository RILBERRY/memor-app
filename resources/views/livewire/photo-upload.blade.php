<div>
    @if (!$photo)
    <div class="flex items-center justify-center w-full" x-show="type === 'with_photo'">
        <label for="dropzone-file"
            class="flex flex-col items-center justify-center w-full h-44 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 20 16">
                    <path stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                </svg>
                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to
                    upload</span></p>
                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF</p>
            </div>

                @error('photo') <span class="text-red-800 dark:text-red-400">This Field is required</span> @enderror
        </label>
    </div>
    @endif
    <input id="dropzone-file"
    wire:model="photo"
    type="file"
    name="image"
    accept="image/*"
    class="hidden"
   />
    <!-- Show Image Preview if photo is uploaded -->
    @if ($photo)
    <div class="my-4  ">
        <img src="{{ $photo->temporaryUrl() }}"
            class="max-w-full h-40 rounded-lg mx-auto"
            alt="Image Preview">
    </div>
    <div class="w-full flex justify-center">
        <button wire:click.prevent="clearPhoto"
            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            Re-Upload
        </button>
    </div>
    @endif
</div>
