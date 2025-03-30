<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ImageUrlDict;
use App\Models\PostGenerateData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Session;

class PostGenerateDataController extends Controller
{
    //
    public function dashboard(Request $request)
    {
        $categories = [
            // January
            'new year' => 'New Year’s Day',
            'orthodox-christmas' => 'Orthodox Christmas',
            'mlk-day' => 'Martin Luther King Jr. Day',

            // February
            'valentines-day' => 'Valentine’s Day',
            'chinese-new-year' => 'Chinese New Year',
            'ash-wednesday' => 'Ash Wednesday',

            // March
            'womens-day' => 'International Women’s Day',
            'st-patricks-day' => 'St. Patrick’s Day',
            'ramadan-start' => 'Start of Ramadan',

            // April
            'easter' => 'Easter Sunday',
            'good-friday' => 'Good Friday',
            'earth-day' => 'Earth Day',

            // May
            'labor-day' => 'Labor Day',
            'mothers-day' => 'Mother’s Day',
            'eid-al-fitr' => 'Eid al-Fitr',

            // June
            'fathers-day' => 'Father’s Day',
            'pride-day' => 'Pride Day',
            'eid-al-adha' => 'Eid al-Adha',

            // July
            'independence-day' => 'Independence Day (USA)',
            'bastille-day' => 'Bastille Day (France)',

            // August
            'friendship-day' => 'Friendship Day',
            'hijri-new-year' => 'Islamic New Year',

            // September
            'teachers-day' => 'Teachers’ Day',
            'grandparents-day' => 'Grandparents’ Day',

            // October
            'halloween' => 'Halloween',
            'navratri' => 'Navratri',
            'diwali' => 'Diwali',

            // November
            'all-saints-day' => 'All Saints’ Day',
            'thanksgiving' => 'Thanksgiving (USA)',
            'black-friday' => 'Black Friday',

            // December
            'world-aids-day' => 'World AIDS Day',
            'hanukkah' => 'Hanukkah',
            'christmas' => 'Christmas',
            'new-years-eve' => 'New Year’s Eve',
        ];


        $PostInfor = null;
        $myCollection = null;
        if ($request->has('PostInfor')) {
            $PostInfor = PostGenerateData::with('category')->where('id', $request->PostInfor)->first();
        }else{
                $myCollection = PostGenerateData::where('user_id', auth()->id())->latest()->get();
        }
        return view('dashboard', compact('PostInfor', 'categories', 'myCollection'));
    }

    public function generateFromSearchImg(Request $request, $id)
    {
        if ($request->has('image_url')) {
            $imageUrl = $request->image_url;
            $PostInfor = PostGenerateData::where('id', $id)->first();
            $this->generatePost($PostInfor, $imageUrl);
            return redirect()->route('dashboard', compact('PostInfor'));
        }
        return redirect()->back()->withErrors('something went wrong');

    }


    public function storeAndCreate(Request $request)
    {
        $params = $request->validate([
            'celebration_title' => 'required',
            'message' => 'nullable',
            'url_slug' => 'nullable',
            'post_path' => 'nullable',
            'published_at' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $params['custom_img_path'] = [
                $request->file('image')->store('custom_images', 'public')
            ];
        } else {
            $paramsCategory = $request->validate([
                'category_name' => 'required',
                'search_by' => 'required',
            ]);
            $existingDict = Category::where('name', $paramsCategory['category_name'])->first();
            if ($existingDict) {
                $responsData = $this->getImagesViaApi($paramsCategory['search_by'], $existingDict->page + 1);
                if ($responsData != null) {
                    $allImages = array_merge($responsData['images'], $existingDict->custom_img_path);
                    $existingDict->update([
                        'custom_img_path' => $allImages,
                        'page' => $responsData['page']
                    ]);
                }
            } else {
                $responsData = $this->getImagesViaApi($paramsCategory['search_by']);
                if ($responsData != null) {
                    $existingDict = Category::create([
                        'name' => $paramsCategory['category_name'],
                        'custom_img_path' => $responsData['images'],
                        'page' => $responsData['page']
                    ]);
                }
            }
            $params['category_id'] = $existingDict->id;
        }

        $params['user_id'] = auth()->id();
        $PostInfor = PostGenerateData::create($params);
        if (!empty($PostInfor->custom_img_path)) {
            $this->generatePost($PostInfor);
        }

        return redirect()->route('dashboard', compact('PostInfor'));
    }

    public function getImagesViaApi($keyWord, $page = 1)
    {
        $apiUrl = 'https://api.pexels.com/v1/search';
        $client = new \GuzzleHttp\Client();

        $response = $client->get($apiUrl, [
            'headers' => [
                'Authorization' => env('pix_api_key')
            ],
            'query' => [
                'query' => $keyWord,
                'page' => $page,
                'per_page' => 15,
            ]
        ]);



        $data = json_decode($response->getBody()->getContents(), true);
        $images = array_map(function ($photo) {
            return $photo['src']['original'];
        }, $data['photos']);

        Session::put('Remaining_Count', $response->getHeader('X-Ratelimit-Remaining')[0]);
        if (count($images) > 0) {
            return [
                'images' => $images,
                'page' => $data['page']
            ];
        }
        return null;
    }

    public function generatePost($PostInfor, $imageUrl = null)
    {
        $mainfontSize = 150;
        $subfontSize = 100;
        $mainHeight = 200;
        $subHeight = 400;
        $recHeight = 500;
        if (!$imageUrl) {
            $imageUrl = $PostInfor['custom_img_path'][0];
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $mainfontSize = 60;
            $subfontSize = 40;
            $mainHeight = 100;
            $subHeight = 150;
            $recHeight = 150;

        }else{
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $tempUrl = 'temp_'. time() . '.' . $extension;
            Storage::disk('public')->put($tempUrl, file_get_contents($imageUrl));
            $imageUrl = $tempUrl;
        }

        $imageCanvas = ImageManager::gd()->read(Storage::disk('public')->get($imageUrl));
        if (!$imageUrl) {
            $imageWidth = $imageCanvas->width();
            $imageHeight = $imageCanvas->height();
            $mainfontSize = (int) (60 / $imageWidth) * 100;
            $subfontSize = (int) (40 / $imageWidth) * 100;
            $mainHeight = 100;
            $subHeight = 150;
            $recHeight = 150;

        }


        $imageCanvas->drawRectangle(0,0, function (RectangleFactory $rectangle) use ( $imageCanvas, $recHeight)  {
            $rectangle->size($imageCanvas->width(), $recHeight); // width & height of rectangle
            $rectangle->background("rgb(255, 255, 255)");
        });


        $imageCanvas->text($PostInfor['celebration_title'], (int)($imageCanvas->width())/2, $mainHeight, function ($font) use ($mainfontSize, $imageCanvas) {
            $font->file(public_path('fonts/OpenSans-Bold.ttf')); // Custom font
            $font->size($mainfontSize);
            $font->color('#000000');
            $font->align('center');
            $font->stroke( '#ffffff', 2 );
        });
        if ($PostInfor['message']) {
            $imageCanvas->text($PostInfor['message'],(int)($imageCanvas->width())/2, $subHeight, function ($font) use ($subfontSize, $imageCanvas) {
            $font->file(public_path('fonts/OpenSans-Regular.ttf'));
            $font->size($subfontSize);
            $font->color('#555555');
            $font->align('center');
            $font->wrap((int)($imageCanvas->width())*.7);
            });
        }
        // if (auth()->user()) {
        //     $imageCanvas->text( '['.auth()->user()->name.']',(int)($imageCanvas->width())/2, 600, function ($font)  {
        //     $font->file(public_path('fonts/OpenSans-Regular.ttf'));
        //     $font->size(40);
        //     $font->color('#555555');
        //     $font->align('center');
        //     });
        // }

        // $canvas->text($PostInfor['date'], 400, 550, function ($font) {
        //     $font->file(public_path('fonts/OpenSans-Regular.ttf'));
        //     $font->size(20);
        //     $font->color('#888888');
        //     $font->align('center');
        // });

        // Save and return
        $fileName = 'generated_' . time(). '.' . $extension;
        $generatedImage = $imageCanvas->scale(800)->encodeByMediaType("image/{$extension}", progressive: true, quality: 60);
        $test = Storage::disk('public')->put($fileName, $generatedImage);
        $PostInfor->update([
            'post_path' => [$fileName]
        ]);

    }
}
