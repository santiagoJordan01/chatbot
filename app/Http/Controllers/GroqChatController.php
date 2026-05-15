<?php

namespace App\Http\Controllers;

use App\Services\GroqClient;
use App\Services\RAGService;
use App\Models\ChatLog;
use Illuminate\Http\Request;

class GroqChatController extends Controller
{
    protected GroqClient $groq;

    public function __construct()
    {
        $this->groq = new GroqClient();
    }

    public function chat(Request $request)
    {
        $request->validate([
            "message" => "required|string",
        ]);

        $message = $request->input("message");
        $useRag = filter_var($request->input("use_rag", false), FILTER_VALIDATE_BOOLEAN);

        try {
            if ($useRag) {
                $rag = new RAGService();
                $result = $rag->answer($message);
            } else {
                $result = $this->groq->chat($message);
            }

            // Persist chat log for auditing/debugging
            try {
                ChatLog::create([
                    "user_id" => $request->user()?->id,
                    "message" => $message,
                    "response" => is_array($result) ? $result : ["text" => (string) $result],
                    "used_rag" => $useRag,
                    "model" => config("groq.model"),
                ]);
            } catch (\Throwable $e) {
                // Failed to save log should not break response; just continue
            }

            return response()->json(["ok" => true, "data" => $result]);
        } catch (\Throwable $e) {
            return response()->json(["ok" => false, "error" => $e->getMessage()], 500);
        }
    }
}

