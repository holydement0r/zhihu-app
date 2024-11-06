<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Question;
use App\Repositories\QuestionRepository;
use App\Topic;
use Auth;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{

    protected $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->middleware('auth')->except(['index', 'show']);

        $this->questionRepository = $questionRepository;
    }

    //所有问题
    public function index()
    {
        $questions = $this->questionRepository->getQuestionsFeed();
        return view('questions.index', compact('questions'));
    }

    //创建问题视图
    public function create()
    {
        return view('questions.create');
    }


    //创建问题
    public function store(StoreQuestionRequest $request)
    {
        // 获取传递的主题名称
        $topicNames = explode(',', $request->get('topics'));

        // 处理并规范化主题
        $topics = $this->questionRepository->normalizeTopics($topicNames);

        // 准备问题数据
        $data = [
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'user_id' => Auth::id(),
        ];

        // 创建问题
        $question = $this->questionRepository->create($data);
        Auth::user()->increment('questions_count');

        // 将主题 ID 与问题关联
        $question->topics()->attach($topics);
        
        // 重定向到问题显示页面
        return redirect()->route('questions.show', [$question->id]);
    }
    

    //显示问题
    public function show($id)
    {
        $question = $this->questionRepository->byIdWithTopicsAndAnswers($id);
        return view('questions.show', compact('question'));
        return $question;
    }

    //修改问题
    public function edit($id)
    {
        $question = $this->questionRepository->byId($id);
        if (Auth::user()->owns($question)) {
            return view('questions.edit', compact('question'));
        }
        return back();
    }

    //问题更新
    public function update(StoreQuestionRequest $request, $id)
    {
        // 获取问题实例
        $question = $this->questionRepository->byId($id);
        
        // 检查问题是否存在
        if (!$question) {
            return redirect()->route('questions.index')->with('error', '问题不存在');
        }
    
        // 从请求中获取话题 ID 数组
        $topics = $request->get('topics'); // 确保此处获取的是 ID 数组
    
        // 更新问题内容
        $question->update([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
        ]);
    
        // 同步话题
        $question->topics()->sync($topics); // 这里使用 sync 可以确保与话题的关系被正确更新
    
        return redirect()->route('questions.show', [$question->id]);
    }
    

    //删除问题
    public function destroy($id)
    {
        $question = $this->questionRepository->byId($id);

        if (Auth::user()->owns($question)) {
            $question->delete();
            Auth::user()->decrement('questions_count');
            return redirect('/');
        }
        abort(403, 'Forbidden');
    }

}
