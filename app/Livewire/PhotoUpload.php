<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class PhotoUpload extends Component
{
    use WithFileUploads;

    public $photo;

    // Handle file upload and validation
    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024', // max size 1MB
        ]);
    }

    // Clear uploaded photo
    public function clearPhoto()
    {
        $this->photo = null;
    }
    public function render()
    {
        return view('livewire.photo-upload');
    }
}
