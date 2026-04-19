<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    /**
     * Get authenticated user from middleware
     */
    private function getAuthenticatedUser(Request $request)
    {
        return Auth::user() ?? $request->auth_user;
    }

    /**
     * List published exams with filters
     * GET /v1/exams
     */
    public function index(Request $request)
    {
        try {
            $query = Exam::where('is_published', true);

            // Search filter
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($request->has('category')) {
                $query->where('category', $request->input('category'));
            }

            // Difficulty filter
            if ($request->has('difficulty')) {
                $query->where('difficulty', $request->input('difficulty'));
            }

            $exams = $query->withCount('questions')
                          ->orderBy('created_at', 'desc')
                          ->get()
                          ->map(function($exam) {
                              return [
                                  'id' => $exam->id,
                                  'title' => $exam->title,
                                  'description' => $exam->description,
                                  'duration_minutes' => $exam->duration_minutes,
                                  'passing_score' => $exam->passing_score,
                                  'total_questions' => $exam->questions_count,
                                  'difficulty' => $exam->difficulty,
                                  'category' => $exam->category,
                                  'price' => $exam->price,
                                  'created_at' => $exam->created_at,
                              ];
                          });

            return response()->json([
                'success' => true,
                'exams' => $exams,
                'total' => $exams->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch exams',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exam detail with questions (hide correct_answer)
     * GET /v1/exams/{id}
     */
    public function show($id)
    {
        try {
            $exam = Exam::with('questions')->findOrFail($id);

            if (!$exam->is_published) {
                return response()->json([
                    'success' => false,
                    'message' => 'Exam not found or not published'
                ], 404);
            }

            $examData = [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'passing_score' => $exam->passing_score,
                'total_questions' => $exam->questions->count(),
                'difficulty' => $exam->difficulty,
                'category' => $exam->category,
                'price' => $exam->price,
                'questions' => $exam->questions->map(function($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'question_type' => $question->question_type,
                        'options' => $question->options,
                        'points' => $question->points,
                        'order_index' => $question->order_index,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'exam' => $examData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exam not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Start exam attempt
     * POST /v1/exams/{id}/start
     */
    public function start(Request $request, $id)
    {
        try {
            $user = $this->getAuthenticatedUser($request);
            if (!$user) {
                return $this->authError();
            }

            $exam = Exam::with('questions')->findOrFail($id);

            if (!$exam->is_published) {
                return response()->json([
                    'success' => false,
                    'message' => 'Exam not available'
                ], 404);
            }

            // Create exam attempt
            $examResult = ExamResult::create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'score' => 0,
                'total_points' => $exam->questions->sum('points'),
                'passed' => false,
                'started_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exam started successfully',
                'attempt_id' => $examResult->id,
                'exam' => [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'duration_minutes' => $exam->duration_minutes,
                    'total_questions' => $exam->questions->count(),
                    'questions' => $exam->questions->map(function($question) {
                        return [
                            'id' => $question->id,
                            'question_text' => $question->question_text,
                            'question_type' => $question->question_type,
                            'options' => $question->options,
                            'points' => $question->points,
                            'order_index' => $question->order_index,
                        ];
                    }),
                ],
                'started_at' => $examResult->started_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit exam answers
     * POST /v1/exams/{id}/submit
     * Body: { "answers": { "question_id": "answer", ... } }
     */
    public function submit(Request $request, $id)
    {
        try {
            $user = $this->getAuthenticatedUser($request);
            if (!$user) {
                return $this->authError();
            }

            $validator = Validator::make($request->all(), [
                'answers' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $exam = Exam::with('questions')->findOrFail($id);
            $answers = $request->input('answers');

            // Calculate score
            $score = 0;
            $totalPoints = $exam->questions->sum('points');

            foreach ($exam->questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                if ($userAnswer === $question->correct_answer) {
                    $score += $question->points;
                }
            }

            $percentage = ($totalPoints > 0) ? ($score / $totalPoints) * 100 : 0;
            $passed = $percentage >= $exam->passing_score;

            // Find or create exam result
            $examResult = ExamResult::where('user_id', $user->id)
                                   ->where('exam_id', $exam->id)
                                   ->whereNull('completed_at')
                                   ->latest()
                                   ->first();

            if (!$examResult) {
                $examResult = ExamResult::create([
                    'user_id' => $user->id,
                    'exam_id' => $exam->id,
                    'score' => $score,
                    'total_points' => $totalPoints,
                    'passed' => $passed,
                    'answers' => $answers,
                    'started_at' => now(),
                    'completed_at' => now(),
                ]);
            } else {
                $examResult->update([
                    'score' => $score,
                    'total_points' => $totalPoints,
                    'passed' => $passed,
                    'answers' => $answers,
                    'completed_at' => now(),
                ]);
            }

            // Send exam result email
            try {
                Mail::to($user->email)->queue(new \App\Mail\ExamResultMail($user, $exam, $examResult));
            } catch (\Exception $e) {
                Log::warning('Failed to send exam result email', ['user_id' => $user->id, 'exam_id' => $exam->id, 'error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => $passed ? 'Congratulations! You passed the exam!' : 'You did not pass the exam. Try again!',
                'result' => [
                    'id' => $examResult->id,
                    'score' => $score,
                    'total_points' => $totalPoints,
                    'percentage' => round($percentage, 2),
                    'passed' => $passed,
                    'passing_score' => $exam->passing_score,
                    'completed_at' => $examResult->completed_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's exam results
     * GET /v1/exams/results
     */
    public function results(Request $request)
    {
        try {
            $user = $this->getAuthenticatedUser($request);
            if (!$user) {
                return $this->authError();
            }

            $results = ExamResult::with('exam')
                                ->where('user_id', $user->id)
                                ->whereNotNull('completed_at')
                                ->orderBy('completed_at', 'desc')
                                ->get()
                                ->map(function($result) {
                                    $percentage = ($result->total_points > 0)
                                        ? ($result->score / $result->total_points) * 100
                                        : 0;

                                    return [
                                        'id' => $result->id,
                                        'exam' => [
                                            'id' => $result->exam->id,
                                            'title' => $result->exam->title,
                                            'difficulty' => $result->exam->difficulty,
                                        ],
                                        'score' => $result->score,
                                        'total_points' => $result->total_points,
                                        'percentage' => round($percentage, 2),
                                        'passed' => $result->passed,
                                        'completed_at' => $result->completed_at,
                                    ];
                                });

            return response()->json([
                'success' => true,
                'results' => $results,
                'total' => $results->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch results',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific result detail
     * GET /v1/exams/results/{id}
     */
    public function resultDetail(Request $request, $id)
    {
        try {
            $user = $this->getAuthenticatedUser($request);
            if (!$user) {
                return $this->authError();
            }

            $result = ExamResult::with(['exam.questions'])
                               ->where('id', $id)
                               ->where('user_id', $user->id)
                               ->firstOrFail();

            $percentage = ($result->total_points > 0)
                ? ($result->score / $result->total_points) * 100
                : 0;

            // Build detailed answers
            $detailedAnswers = [];
            foreach ($result->exam->questions as $question) {
                $userAnswer = $result->answers[$question->id] ?? null;
                $isCorrect = ($userAnswer === $question->correct_answer);

                $detailedAnswers[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->question_text,
                    'options' => $question->options,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $question->correct_answer,
                    'is_correct' => $isCorrect,
                    'points' => $isCorrect ? $question->points : 0,
                    'max_points' => $question->points,
                ];
            }

            return response()->json([
                'success' => true,
                'result' => [
                    'id' => $result->id,
                    'exam' => [
                        'id' => $result->exam->id,
                        'title' => $result->exam->title,
                        'description' => $result->exam->description,
                        'difficulty' => $result->exam->difficulty,
                    ],
                    'score' => $result->score,
                    'total_points' => $result->total_points,
                    'percentage' => round($percentage, 2),
                    'passed' => $result->passed,
                    'started_at' => $result->started_at,
                    'completed_at' => $result->completed_at,
                    'answers' => $detailedAnswers,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Result not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
