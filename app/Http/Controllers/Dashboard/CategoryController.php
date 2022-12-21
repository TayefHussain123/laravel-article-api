<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Response;
use App\Enum\EntityStatus;
use Illuminate\Support\Str;
use App\Models\Category;


class CategoryController extends Controller
{

    // create new category

    public function createCategory(Request $request){

        $validators=Validator::make($request->all(),[
            'category_name'     =>  'required|min:2|max:100|unique:categories',
         ]);

        if($validators->fails()){
            return Response::json(['message'=>$validators->getMessageBag()->toArray()],422);
        }else{
            $category=new Category();
            $category->category_name=$request->category_name;
            $category->category_slug = Category::where('category_slug', '=', Str::slug($request->category_name))->first() != null ? Str::slug(Str::substr($request->category_name,0, (140) )).'-'.time() : Str::slug((Str::substr($request->category_name,0, (140) )));
            $category->category_status=EntityStatus::Active;
            $category->save();
            return Response::json(['category'=> $category],201);
        }

    }


    // update category by category id

    public function updateCategoryByCategoryRdbmsId(Request $request,$id){


        if($id < 1){
            return Response::json(['message'=> "Invalid categoryRdbmsId!"],400);
        }

        $category = Category::find($id);

        if(!$category){
            return Response::json(['message'=>'Category not found by this categoryRdbmsId!'],204);
        }else{

            if ($request->category_name == $category->category_name){
                return Response::json(['message'=> $category],200);
            }

            $validators=Validator::make($request->all(),[
                'category_name'=>'required|min:2|max:100|unique:categories',
             ]);

             if($validators->fails()){
                return Response::json(['message'=>$validators->getMessageBag()->toArray()],422);
            }else{

                $category->update([
                        'category_name'      => $request->category_name,
                        'category_slug'      =>  Category::where('category_slug', '=', Str::slug($request->category_name))->first() != null ? Str::slug(Str::substr($request->category_name,0, (140) )).'-'.time() : Str::slug((Str::substr($request->category_name,0, (140) )))
                 ]);
                return Response::json(['category'=>$category],200);
            }
        }


    }

    // find all category

    public function findAllCategory(){
        $categories = Category::orderBy('id','DESC')->paginate(15);

        if($categories){
            return Response::json(['categories'=>$categories],200,[],JSON_UNESCAPED_SLASHES);

        }else{
            return Response::json(['message'=>'No category found!'],204);
        }

     }


    // find category by category id

    public function findCategoryByCategoryRdbmsId($id){

        if($id < 1){
            return Response::json(['message'=> "Invalid categoryRdbmsId!"],400);
        }

        $category = Category::where('id',$id)->first();

        if($category){
            return Response::json(['category'=>$category],200);

        }else{
            return Response::json(['message'=>'Category not found!'],204);
        }
    }





    // delete category by category id

    public function deleteCategoryByCategoryRdbmsId(Request $request, $id){

        if($id < 1){
            return Response::json(['message'=> "Invalid categoryRdbmsId!"],400);
        }

        $category = Category::find($id);

        if(!$category){
            return Response::json(['message'=> "Category not found by this categoryRdbmsId!"],204);
        }else{
            try{
                $category=Category::where('id',$request->id)->first();
                if($category){
                    $category->delete();
                    return Response::json(['message'=> "Category deleted successfully!"],204);
                }else{
                    return Response::json(['message'=> "Category not found!"],204);
                }
            }catch(\Illuminate\Database\QueryException $exception){
                return Response::json(['message'=> "Category belongs to an article.So you can not delete this category!"],422);
            }
        }
    }

    // search category by keyword
    public function searchCategoryByKeyword(Request $request){
        $categories=Category::where('category_name','LIKE','%'.$request->keyword.'%')->orWhere('category_slug','LIKE','%'.$request->keyword.'%')->paginate(15);
        if($categories == null || $categories->isEmpty()){
            return Response::json(['message'=>'No category match found!'],204);
        }else{
            return Response::json(['categories'=>$categories],200,[],JSON_UNESCAPED_SLASHES);
        }
    }
}
