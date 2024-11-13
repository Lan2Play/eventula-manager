<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Helpers;

use App\NewsArticle;
use App\NewsComment;
use App\NewsTag;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

class NewsController extends Controller
{
    /**
     * Show News Index Page
     * @return View
     */
    public function index()
    {
        $seoKeywords = Helpers::getSeoKeywords();
        $seoKeywords[] = "News";
        SEOMeta::addKeyword($seoKeywords);
        OpenGraph::addProperty('type', 'article');
        return view('news.index')
            ->with('newsArticles', NewsArticle::paginate(20));
    }

    /**
     * Show News Article Page
     * @param  NewsArticle $newsArticle
     * @return View
     */
    public function show(NewsArticle $newsArticle)
    {
        $seoKeywords = Helpers::getSeoKeywords();
        $seoKeywords[] = $newsArticle->title;
        foreach ($newsArticle->tags as $tag) {
            $seoKeywords[] = $tag->tag;
        }
        SEOMeta::setDescription(Helpers::getSeoCustomDescription($newsArticle->title));
        SEOMeta::addKeyword($seoKeywords);
        OpenGraph::setDescription(Helpers::getSeoCustomDescription($newsArticle->title));
        OpenGraph::addProperty('type', 'article');
        return view('news.show')
            ->with('newsArticle', $newsArticle);
    }

    /**
     * Show News Articles for Given Tag
     * @param  NewsTag $newsTag
     * @return View
     */
    public function showTag(NewsTag $newsTag)
    {
        $seoKeywords = Helpers::getSeoKeywords();
        $seoKeywords[] = $newsTag->tag;
        SEOMeta::setDescription(Helpers::getSeoCustomDescription($newsTag->tag));
        SEOMeta::addKeyword($seoKeywords);
        OpenGraph::setDescription(Helpers::getSeoCustomDescription($newsTag->tag));
        OpenGraph::addProperty('type', 'article');
        foreach (NewsTag::where('tag', $newsTag->tag)->get()->reverse() as $newsTag) {
            $newsArticles[] = $newsTag->newsArticle;
        }
        return view('news.tag')
            ->with('tag', $newsTag->tag)
            ->with('newsArticles', $newsArticles);
    }

    /**
     * Store News Article Comment
     * @param  NewsArticle $newsArticle
     * @param  Request $request
     * @return View
     */
    public function storeComment(NewsArticle $newsArticle, Request $request)
    {
        if (!Auth::user()) {
            $request->session()->flash('alert-danger', __('news.please_login'));
            return Redirect::to('login');
        }
        $rules = [
            'comment'       => 'required|filled|max:200',
        ];
        $messages = [
            'comment.required'      => __('news.comment_required'),
            'comment.filled'        => __('news.comment_cannot_be_empty'),
            'comment.max'           => __('news.comment_max_characters'),
        ];
        $this->validate($request, $rules, $messages);

        $comment = [
            'comment'       => trim($request->comment),
            'news_feed_id'  => $newsArticle->id,
            'user_id'       => Auth::id()
        ];
        if (!NewsComment::create($comment)) {
            $request->session()->flash('alert-danger',  __('news.comment_cannot_post'));
            return Redirect::back();
        }
        $request->session()->flash('alert-success', __('news.comment_posted_waiting_approval'));
        return Redirect::back();
    }

    /**
     * Report News Article Comment
     * @param  NewsArticle $newsArticle
     * @param  NewsComment $newsComment
     * @return View
     */
    public function reportComment(NewsArticle $newsArticle, NewsComment $newsComment, Request $request)
    {
        if (!Auth::user()) {
            $request->session()->flash('alert-danger', __('news.please_login'));
            return Redirect::to('login');
        }
        if (!$newsComment->report()) {
            $request->session()->flash('alert-danger', __('news.comment_cannot_report'));
            return Redirect::back();
        }
        $request->session()->flash('alert-success', __('news.comment_reported_waiting_review'));
        return Redirect::back();
    }

    /**
     * Report News Article Comment
     * @param  NewsArticle $newsArticle
     * @param  NewsComment $newsComment
     * @return View
     */
    public function destroyComment(NewsArticle $newsArticle, NewsComment $newsComment, Request $request)
    {
        if (!Auth::user()) {
            $request->session()->flash('alert-danger', __('news.please_login'));
            return Redirect::to('login');
        }
        if (Auth::id() != $newsComment->user_id) {
            $request->session()->flash('alert-danger', __('news.comment_delete_not_your'));
            return Redirect::back();
        }
        if (!$newsComment->delete()) {
            $request->session()->flash('alert-danger', __('news.comment_cannot_delete'));
            return Redirect::back();
        }
        $request->session()->flash('alert-success', __('news.comment_deleted'));
        return Redirect::back();
    }

    /**
     * Edit News Article Comment
     * @param  NewsArticle $newsArticle
     * @param  NewsComment $newsComment
     * @return View
     */
    public function editComment(NewsArticle $newsArticle, NewsComment $newsComment, Request $request)
    {
        if (!Auth::user()) {
            $request->session()->flash('alert-danger', __('news.please_login'));
            return Redirect::to('login');
        }
        $rules = [
            'comment_modal'         => 'filled|max:200',
        ];
        $messages = [
            'comment_modal.filled'  => __('news.comment_cannot_be_empty'),
            'comment_modal.max'     => __('news.comment_max_characters'),
        ];
        $this->validate($request, $rules, $messages);

        if (Auth::id() != $newsComment->user_id) {
            $request->session()->flash('alert-danger', __('news.comment_edit_not_your'));
            return Redirect::back();
        }
        if (!$newsComment->edit($request->comment_modal)) {
            $request->session()->flash('alert-danger', __('news.comment_cannot_edit'));
            return Redirect::back();
        }
        $request->session()->flash('alert-success', __('news.comment_edited'));
        return Redirect::back();
    }
}
