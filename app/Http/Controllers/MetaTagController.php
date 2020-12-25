<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaTag;

class MetaTagController extends Controller
{
    public function loadAll(Request $request){
        $meta_tags = MetaTag::all();
        $meta_tags_array=[];
        foreach($meta_tags as $tag){
            $tag_row;
            $tag_row['id'] = $tag->id;
            $tag_row['meta_tag'] = "<textarea disabled class='form-control' style='direction:ltr;width:100%;height: 36px;min-height: 36px;margin-top:-3px;padding-top: 3px;'> ".$tag->meta_tag." </textarea>";
            $meta_tags_array[] = $tag_row;
        }
        return response()->json(['data' => $meta_tags_array,]);
    }


    public function addMetaTag(Request $request){
        
        $meta_tag = $request->meta;
        $create = MetaTag::create([
            'meta_tag' => $meta_tag,
        ]);

        if(!$create){
            return response()->json([
                'status' => 'error',
                'message' => 'فشلت العملية حاول مجددا !',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "تمت عملية إضافة الوسم بنجاح",
        ]);
    }

    public function fetchOne(Request $request){
        
        $meta = MetaTag::find($request->id);
        return response()->json([
            'status' => 'success',
            'message' => 'ok',
            'meta' => $meta,
        ]);

    }



    public function update(Request $request){
        
        $id = $request->id;
        $meta = $request->meta;

        $update = MetaTag::where('id', $id)->update([
            'meta_tag' => $meta,
        ]);

        if(!$update){
            return response()->json([
                'status' => 'error',
                'message' => 'فشلت العملية حاول مجددا !',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "تمت عملية تحديث الوسم بنجاح",
        ]);
    }/* /update() */

    public function delete(Request $request){
        $deleteMeta = MetaTag::where('id',$request->id)->delete();
        if(!$deleteMeta){
            return response()->json([ 'status' => 'error', 'message' => 'Something went wrong!' ]);
        }
        return response()->json([ 'status' => 'success', 'message' => 'تمت عملية الحذف بنجاح.' ]);
    }/* /delete() */
}
