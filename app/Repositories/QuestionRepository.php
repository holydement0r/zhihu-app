<?php
namespace App\Repositories;


use App\Question;
use App\Topic;

class QuestionRepository
{
    //拿到问题的topics和答案
    public function byIdWithTopicsAndAnswers($id)
    {
        return Question::where('id', $id)->with(['topics','answers'])->first();
    }

    public function create(array $attributes)
    {
        return Question::create($attributes);
    }

    //更新问题的标签
    public function normalizeTopics(array $topics)
    {
        return collect($topics)->map(function ($topic) {
            // 查找现有话题
            $existingTopic = Topic::where('name', $topic)->first();
    
            if ($existingTopic) {
                // 增加计数
                $existingTopic->increment('questions_count');
                return $existingTopic->id; // 返回 ID
            }
    
            // 创建新话题并返回 ID
            $newTopic = Topic::create(['name' => $topic, 'questions_count' => 1]);
            return $newTopic->id;
        })->toArray();
    }
    


    public function byId($id)
    {
        return Question::find($id);
    }

    //拿到所有问题
    public function getQuestionsFeed()
    {
        return Question::published()->orderBy('is_first','desc')->latest('updated_at')->with('user')->get();
    }

    //指定问题的评论
    public function getQuestionCommentsById($id)
    {
        $question = Question::with('comments', 'comments.user')->where('id', $id)->first();

        return $question->comments;
    }

    //增问题的评论数
    public function addCommentsCount($id)
    {
        $question = Question::find($id);
        $question->increment('comments_count');
    }
}