<?php

namespace App\Livewire;

use Livewire\Component;

class Autocomplete extends Component
{
    public $query = '';
    public $results = [];
    public $options = [];
    public $label = 'Search...';
    public $name = '';

    public function mount($options = [], $label = 'Search...', $name = '')
    {
        $this->options = $options;
        $this->label = $label;
        $this->name = $name;
        $this->results = $options;
    }

    public function updatedQuery()
    {
        if (!empty($this->query)) {
            $this->results = array_filter($this->options, function ($label) {
                return stripos($label, $this->query) !== false;
            });
        } else {
            $this->results = $this->options;
        }
    }

    public function selectItem($key)
    {
        $this->query = $this->options[$key] ?? $key;
        $this->results = [];
    }
    public function render()
    {
        return view('livewire.autocomplete');
    }
}
