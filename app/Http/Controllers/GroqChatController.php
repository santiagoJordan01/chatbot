<?php

namespace App\Http\Controllers;

use App\Services\ClinicReplyGuard;
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
            'message' => 'required|string',
            'history' => 'sometimes|array|max:8',
            'history.*.role' => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:4000',
        ]);

        $message = $request->input('message');
        $useRag = filter_var($request->input('use_rag', true), FILTER_VALIDATE_BOOLEAN);
        $history = collect($request->input('history', []))
            ->map(fn (array $item) => [
                'role' => $item['role'],
                'content' => $item['content'],
            ])
            ->all();

        try {
            if ($useRag) {
                $rag = new RAGService();
                $result = $rag->answer($message, 3, $history);
            } else {
                $result = $this->groq->chat($message, [
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => (string) config('groq.system_prompt')]],
                        $history,
                        [['role' => 'user', 'content' => $message]],
                    ),
                ]);
            }

            $result = $this->guardReply($result, $message, $history);

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

    protected function guardReply(array $result, string $message, array $history): array
    {
        $content = $result['choices'][0]['message']['content'] ?? null;
        if (! is_string($content)) {
            return $result;
        }

        $result['choices'][0]['message']['content'] = (new ClinicReplyGuard())->apply($message, $history, $content);

        return $result;
    }
}

