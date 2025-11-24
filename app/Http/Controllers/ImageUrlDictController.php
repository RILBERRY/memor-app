<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\PostGenerateData;

class ImageUrlDictController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();
        Log::info("Telegram Update:", $update);

        if (!isset($update['message'])) {
            return response()->json(['error' => 'No message'], 400);
        }

        $message = $update['message'];
        $chatId  = $message['chat']['id'];
        $username = $message['from']['username'] ?? null;

        $botToken = '7360176063:AAFEAR2Xh9Ru6-gXhMZV1SmK6cigyJkfY3g';

        // ---------- AUTHORIZED USERS ----------
        $allowedUsernames = ['a_rilwan'];
        if (!in_array($username, $allowedUsernames)) {
            $this->sendTelegramText($botToken, $chatId, "❌ You are not authorized.");
            return;
        }

        // ---------- TEXT DATA ----------
        $text = trim($message['text'] ?? '');

        // ---------- PHOTO HANDLING ----------
        $fileContent = null;
        $savedFilePath = null;

        if (isset($message['photo'])) {
            // Get highest resolution photo
            $photo = end($message['photo']);
            $fileId = $photo['file_id'];

            // 1️⃣ Get Telegram file path
            $fileInfo = Http::get("https://api.telegram.org/bot{$botToken}/getFile", [
                'file_id' => $fileId
            ])->json();

            if (!isset($fileInfo['result']['file_path'])) {
                Log::error("No file_path returned");
                return;
            }

            $telegramPath = $fileInfo['result']['file_path'];

            // 2️⃣ Download file
            $fileContent = Http::get("https://api.telegram.org/file/bot{$botToken}/{$telegramPath}")->body();

            // 3️⃣ Save file locally
            $folder = public_path('zooarea');
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            $filename = time() . "_" . basename($telegramPath);
            $savedFilePath = $folder . '/' . $filename;

            file_put_contents($savedFilePath, $fileContent);

            Log::info("Image saved:", ["path" => $savedFilePath]);
        }

        // ---------- PROCESS COMMAND ----------
        $post = $this->dataRece($text, $chatId, $botToken, $fileContent, $savedFilePath);

        if (!$post) {
            $this->sendTelegramText($botToken, $chatId, "⚠️ Something went wrong.");
            return;
        }

        // ---------- RESPONSE TYPES ----------
        if ($post['type'] == "auth") {
            $this->sendTelegramText($botToken, $chatId, "🔐 Auth token saved.");
            return;
        }

        if ($post['type'] == "rece") {
            $this->sendTelegramText($botToken, $chatId, "📤 Sent to API successfully.");
            return;
        }

        if ($post['type'] == "vid") {
            $this->sendTelegramMedia($botToken, $chatId, $post['vid'], "Here is your file");
            return;
        }
    }

    // ---------------------------------------------------
    // PROCESS INCOMING TEXT / MEDIA
    // ---------------------------------------------------
    function dataRece($line, $chatId, $botToken, $fileContent = null, $savedFilePath = null)
    {
        // -------- SAVE AUTH TOKEN --------
        if (str_starts_with(strtolower($line), 't-')) {
            $tokenValue = substr($line, 2);

            Category::updateOrCreate(
                ['name' => 'token'],
                ['custom_img_path' => ['auth' => $tokenValue]]
            );

            return ['type' => "auth"];
        }

        // -------- RETURN VIDEO FROM DB --------
        if (str_starts_with(strtolower($line), 'v-')) {
            $id = substr($line, 2);
            $post = PostGenerateData::find($id);

            if (!$post) {
                return ['type' => 'error'];
            }

            return [
                "type" => 'vid',
                "vid" => $post->video_path ?? null
            ];
        }

        // -------- SEND FILE TO API --------
        if ($fileContent) {

            $cat = Category::where('name', 'token')->first();
            $token = $cat?->custom_img_path['auth'] ?? null;

            if (!$token) return ['type' => 'error'];

            $client = new \GuzzleHttp\Client();

            // Example API request
            $response = $client->post('https://api.grtkniv.net/api/videoGenerations/animate', [
                'headers' => [
                    'Authorization' => $token,
                ],
                'multipart' => [
                    [
                        'name' => 'name',
                        'contents' => $line,
                    ],
                    [
                        'name' => 'webhook',
                        'contents' => 'https://zookates.guaranteefuel.net/webhook/receiver',
                    ],
                    [
                        'name' => 'image',
                        'contents' => $fileContent,
                        'filename' => basename($savedFilePath),
                    ],
                ],
            ]);

            return [
                "type" => 'rece',
                "vid" => (string)$response->getBody()
            ];
        }

        return null;
    }

    // ---------------------------------------------------
    // TELEGRAM HELPERS
    // ---------------------------------------------------

    private function sendTelegramText($token, $chatId, $text)
    {
        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text'    => $text,
        ]);
    }

    private function sendTelegramMedia($token, $chatId, $filePath, $caption = "")
    {
        if (!file_exists($filePath)) {
            $this->sendTelegramText($token, $chatId, "❌ File not found");
            return;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            Http::attach('photo', file_get_contents($filePath), basename($filePath))
                ->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                    'chat_id' => $chatId,
                    'caption' => $caption
                ]);
        } else {
            Http::attach('video', file_get_contents($filePath), basename($filePath))
                ->post("https://api.telegram.org/bot{$token}/sendVideo", [
                    'chat_id' => $chatId,
                    'caption' => $caption
                ]);
        }
    }
}
