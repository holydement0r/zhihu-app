<?php
namespace App\Http\Controllers\Admin;

use App\Repositories\TopicRepository;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TopicsController extends Controller
{
    private $topic;
    public function __construct(TopicRepository $topic)
    {
        $this->topic = $topic;
        $this->middleware('admin');
    }
    //标签列表
    public function index(Request $request)
    {
        $keyword = $request->input('s_title');
        $topics = $this->topic->getTopicsFeed();
    
        if ($keyword) {
            $topics = $topics->filter(function ($topic) use ($keyword) {
                return stripos($topic->name, $keyword) !== false; // 根据名称进行搜索
            });
        }
    
        return view('admin.topics.index', compact('topics'));
    }

    public function store()
    {

    }

    //删除标签
    public function destroy($id)
    {
        $comment = $this->topic->byId($id);
        $comment->delete();
        return redirect()->route('admin.topics');
    }
}