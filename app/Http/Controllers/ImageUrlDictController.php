<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageUrlDictController extends Controller
{
     public function handle(Request $request)
    {
        $update = $request->all();


        if (!isset($update['message'])) {
            return response()->json(['error' => 'No message'], 400);
        }

        $message = $update['message'];

        $allowedUsernames = ['a_rilwan'];

        $botToken = '7360176063:AAFEAR2Xh9Ru6-gXhMZV1SmK6cigyJkfY3g';

        if (!in_array($update['message']['from']['username'], $allowedUsernames)) {
            $reply = 'you are not authorized';

            $chatId = $message['chat']['id'];

            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $reply,
            ]);

            return;

        }

        $messageItems = explode("#", $update['message']['text']);
        $item =  trim($update['message']['text']);
        $post = $this->dataRece($item);
      if($post['type'] == "auth"){
            $reply = ' Total : Records created successfully';
        }
        if ($post['type'] == "vid") {

    // Check if file exists
        if (file_exists($post['vid'])) {

            // Determine mime type to check if it's image or video
            $ext = strtolower(pathinfo($post['vid'], PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {

                // Send IMAGE
                Http::attach(
                    'photo',
                    file_get_contents($post['vid']),
                    basename($post['vid'])
                )->post("https://api.telegram.org/bot{$botToken}/sendPhoto", [
                    'chat_id' => $chatId,
                    'caption' => $reply ?? ''
                ]);

            } elseif (in_array($ext, ['mp4', 'mov', 'mpeg', 'avi', 'mkv'])) {

                // Send VIDEO
                Http::attach(
                    'video',
                    file_get_contents($post['vid']),
                    basename($post['vid'])
                )->post("https://api.telegram.org/bot{$botToken}/sendVideo", [
                    'chat_id' => $chatId,
                    'caption' => $reply ?? ''
                ]);

            } else {
                // Unsupported file type
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Unsupported media format"
                ]);
            }

        } else {
            // File missing
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "File not found"
            ]);
        }

} else {
    // Text message fallback
    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
        'chat_id' => $chatId,
        'text' => $reply ?? "something wrong",
    ]);
}

    }

    function dataRece($line){

        if (str_starts_with(strtolower($line), 't-')) {
            $data = substr($line, 2);
            $token = Category::where('name', 'token')->first();

            $token->update(['custom_img_path' : ['auth': $data] ]);
            return ['type':"auth"];
        }

        if (str_starts_with(strtolower($line), 'v-')) {
            $data = substr($line, 2);
            $post = PostGenerateData::where('id',$data)->first();

            return [
                "type" : 'vid',
                "vid" : $post
            ];
        
        }
        

    }
}
