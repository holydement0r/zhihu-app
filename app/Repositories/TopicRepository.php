<?php


namespace App\Repositories;


use App\Topic;
use Illuminate\Http\Request;

class TopicRepository
{
    protected $topic;


    public function getTopicsForTagging(Request $request) //根据用户输入的查询字符串（q）从数据库中查找匹配的主题
    {
        $topics = Topic::select(['id', 'name'])
            ->where('name', 'like', '%' . $request
                    ->query('q') . '%')->get();
        return $topics;
    }

    public function getTopicsFeed()
    {
        return Topic::all();
    }

    public function byId($id)
    {
        return Topic::find($id);
    }
}