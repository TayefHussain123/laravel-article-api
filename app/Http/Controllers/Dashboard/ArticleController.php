<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Response;
use App\Enum\EntityStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    // create new article

    public function createArticle(Request $request){

        $validators=Validator::make($request->all(),[
            'article_title'     =>  'required|min:2|max:500|unique:articles',
            'article_body'      =>  'required|min:2|max:1500',
            'author_id'         =>  'required|numeric|exists:users,id',
            'category_id'       =>  'required|numeric|exists:categories,id',
         ]);

        if($validators->fails()){
            return Response::json(['message'=>$validators->getMessageBag()->toArray()],422);
        }else{


          $articleImageUrl = $request->article_image;

            if ($request->has('article_image') && $request->article_image != null) {

                $validatorsImage=Validator::make($request->all(),[
                    'article_image' => ['required', 'mimes:jpeg,jpg,png','max:512|dimensions:width=1920,height=1080',
                ]
                 ]);
                 if($validatorsImage->fails()){
                    return Response::json(['message'=> $validatorsImage->getMessageBag()->toArray()],422);
                 }

                $articleImageName = time() . '.' . $request->article_image->getClientOriginalExtension();
                $request->article_image->storeAs('public/article/images/', $articleImageName);
                $this->articleImageUrl = Url::asset('storage/article/images/' . $articleImageName);
            }

            $article=new Article();
            $article->article_title     =   $request->article_title;
            $article->article_slug      =   Article::where('article_slug', '=', Str::slug(Str::substr($request->article_title,0, (600) )))->first() != null ? Str::slug(Str::substr($request->article_title,0, (600) )).'-'.time() : Str::slug((Str::substr($request->article_title,0, (600) )));
            $article->article_body      =   $request->article_body;
            $article->image_url_name    =   $articleImageUrl !=null ? $this->articleImageUrl  : null;
            $article->author_id         =   $request->author_id;
            $article->category_id       =   $request->category_id;
            $article->article_status    =   EntityStatus::Active;
            $article->save();

            return Response::json(['article'=> ArticleResource::make($article)],201,[],JSON_UNESCAPED_SLASHES);
        }
    }


    // update article by article id

    public function updateArticleByArticleRdbmsId(Request $request, $id){


        if($id < 1){
            return Response::json(['message'=> "Invalid articleRdbmsId!"],400);
        }

        $article = Article::find($id);

        if(!$article){
            return Response::json(['message'=> 'Article not found by this articleRdbmsId!'],204);
        }

        if ($request->has('article_title') && $request->article_title != null) {

            $validators=Validator::make($request->all(),[
                'article_title'     =>  'required|min:2|max:500',
             ]);

             if($validators->fails()){
                return Response::json(['message'=>$validators->getMessageBag()->toArray()],422);
            }

            if ($request->article_title != $article->article_title){
                $article->update([
                    'article_title'     =>  $request->article_title,
                    'article_slug'      =>  Article::where('article_slug', '=', Str::slug(Str::substr($request->article_title,0, (600) )))->first() != null ? Str::slug(Str::substr($request->article_title,0, (600) )).'-'.time() : Str::slug((Str::substr($request->article_title,0, (600) )))
                    ]);
            }
    }

    if ($request->has('article_body') && $request->article_body != null) {


        $validators=Validator::make($request->all(),[
            'article_body'      =>  'required|min:2|max:1500',
         ]);
         if($validators->fails()){
            return Response::json(['message'=>$validators->getMessageBag()->toArray()],422);
        }else{
            $article->update([
                'article_body'      =>  $request->article_body,
            ]);
        }

    }

    if ($request->has('category_id') && $request->category_id != null) {


        $validators=Validator::make($request->all(),[
            'category_id' => 'required|numeric|exists:categories,id'
         ]);
         if($validators->fails()){
            return Response::json(['message'=>$validators->getMessageBag()->toArray()],422);
        }else{
            $article->update([
                'category_id'       =>  $request->category_id

            ]);
        }


    }
    if ($request->has('article_image') && $request->article_image != null) {


        $validatorsImage=Validator::make($request->all(),[
            'article_image' => ['required', 'mimes:jpeg,jpg,png','max:512|dimensions:width=500,height=500',
        ]
         ]);
         if($validatorsImage->fails()){
            return Response::json(['message'=> $validatorsImage->getMessageBag()->toArray()],422);
         }

        $file = 'storage/article/images/' . $article->article_image;
        File::delete($file);

        $articleImageName = time() . '.' . $request->article_image->getClientOriginalExtension();
        $request->article_image->storeAs('public/article/images/', $articleImageName);
        $articleImageUrl = Url::asset('storage/article/images/' . $articleImageName);

        $article->update([
            'article_image_url' => $articleImageUrl
        ]);
    }

    return Response::json(['article'=> ArticleResource::make($article)],200,[],JSON_UNESCAPED_SLASHES);
}

    // find all articles

    public function findAllArticles(){

        $articles =  ArticleResource::make(Article::orderBy('id','DESC'))->paginate(15);

        if($articles){

            return Response::json(['articles'=> $articles],201,[],JSON_UNESCAPED_SLASHES);
        }else{
            return Response::json(['message'=>"No article found!"],204);
        }

     }


     // find article by article id

    public function findArticleByArticleRdbmsId($id){

        if($id < 1){
            return Response::json(['message'=> "Invalid articleRdbmsId!"],400);
        }
        $article = Article::find($id);

        if($article){
            return Response::json(['article'=>  ArticleResource::make($article)],201,[],JSON_UNESCAPED_SLASHES);
        }else{
            return Response::json(['message'=>'No article found by this articleRdbmsId!'],204);
        }
    }
    // delete article by article id

    public function deleteArticleByArticleRdbmsId(Request $request, $id){

        if($id < 1){
            return Response::json(['message'=>"Invalid articleRdbmsId!"],400);
        }
        $article = Article::find($id);
              if($article){
                $article->delete();
                return Response::json(['message'=> "Article deleted successfully"],204);
                }else{
                    return Response::json(['message'=>'Article not found by this articleRdbmsId!'],204);
                }
        }

    // search article by keyword
    public function searchArticleByKeyword(Request $request){
        $articles = ArticleResource::make(Article::where('article_title','LIKE','%'.$request->keyword.'%')->orWhere('article_slug','LIKE','%'.$request->keyword.'%')->orderBy('id','DESC')->orderBy('id','DESC'))->paginate(15);

        if($articles == null || $articles->isEmpty()){
            return Response::json(['message'=> "No article match found!"],204);
        }else{
            return Response::json(['articles'=> $articles],200,[],JSON_UNESCAPED_SLASHES);
        }
    }

}
