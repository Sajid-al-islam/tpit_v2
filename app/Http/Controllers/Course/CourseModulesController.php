<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\CourseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ContactMessage;
use App\Models\Course\CourseMilestone;
use App\Models\Course\CourseModule;
use App\Models\Course\CourseModuleClasses;
use App\Models\Course\CourseModuleClassRoutines;

class CourseModulesController extends Controller
{
    public function all()
    {
        $paginate = (int) request()->paginate ?? 10;
        $orderBy = request()->orderBy ?? 'id';
        $orderByType = request()->orderByType ?? 'ASC';

        $status = 1;
        if (request()->has('status')) {
            $status = request()->status;
        }

        $query = CourseModule::where('status', $status)->orderBy($orderBy, $orderByType);

        if (request()->has('search_key')) {
            $key = request()->search_key;
            $query->where(function ($q) use ($key) {
                return $q->where('id', '%' . $key . '%')
                ->orWhere('course_id', '%' . $key . '%')
                ->orWhere('module_no', '%' . $key . '%')
                ->orWhere('title', 'LIKE', '%' . $key . '%');
            });
        }

        $datas = $query->paginate($paginate);
        return response()->json($datas);
    }

    public function show($id)
    {

        $select = "*";
        if (request()->has('select_all') && request()->select_all) {
            $select = "*";
        }
        $data = CourseModule::where('id', $id)
            ->select($select)
            ->first();
        if ($data) {
            return response()->json($data, 200);
        } else {
            return response()->json([
                'err_message' => 'data not found',
                'errors' => [
                    'user' => [],
                ],
            ], 404);
        }
    }
    public function store()
    {
        // dd(request()->all());
        $validator = Validator::make(request()->all(), [
            'modules' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach(request()->modules as $course_data) {
            $course_data = (object) $course_data;
            $milestone = new CourseMilestone();
            $milestone->title = $course_data->title;
            $milestone->save();
            foreach ($course_data->modules as $key => $module) {
                $module = (object) $module;
                $data = new CourseModule();
                $data->module_no = $module->module_no;
                $data->title = $module->title;
                $data->save();

                foreach($module->classes as $key => $class) {
                    $class = (object) $class;
                    $course_class = new CourseModuleClasses();
                    $course_class->class_no = $class->class_no;
                    $course_class->title = $class->title;
                    $course_class->type = $class->type;
                    $course_class->class_video_link = $class->class_video_link;
                    $course_class->class_video_poster = $class->class_video_poster;
                    $course_class->save();

                    $routine = new CourseModuleClassRoutines();
                    $class_routines = (object) $class->routine;
                    $routine->date = $class_routines->date;
                    $routine->time = $class_routines->time;
                    $routine->topic = $class_routines->topic;
                    $routine->save();
                }
            }
        }

        return response()->json(['message' => 'module updated successfully!'], 200);
    }

    public function canvas_store()
    {
        $validator = Validator::make(request()->all(), [
            'course_id' => ['required'],
            'module_no' => ['required'],
            'title' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = new CourseModule();
        $data->course_id = request()->course_id;
        $data->module_no = request()->module_no;
        $data->title = request()->title;
        $data->save();

        return response()->json($data, 200);
    }

    public function update()
    {
        $data = CourseModule::find(request()->id);
        if(!$data){
            return response()->json([
                'err_message' => 'validation error',
                'errors' => ['name'=>['user_role not found by given id '.(request()->id?request()->id:'null')]],
            ], 422);
        }

        $validator = Validator::make(request()->all(), [
            'course_id' => ['required'],
            'module_no' => ['required'],
            'title' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data->course_id = request()->course_id;
        $data->module_no = request()->module_no;
        $data->title = request()->title;
        $data->save();

        return response()->json($data, 200);
    }

    public function canvas_update()
    {
        $data = CourseModule::find(request()->id);
        if(!$data){
            return response()->json([
                'err_message' => 'validation error',
                'errors' => ['name'=>['user_role not found by given id '.(request()->id?request()->id:'null')]],
            ], 422);
        }

        $validator = Validator::make(request()->all(), [
            'course_id' => ['required'],
            'module_no' => ['required'],
            'title' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data->course_id = request()->course_id;
        $data->module_no = request()->module_no;
        $data->title = request()->title;
        $data->save();

        return response()->json($data, 200);
    }

    public function soft_delete()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:course_modules,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = CourseModule::find(request()->id);
        $data->status = 'inactive';
        $data->save();

        return response()->json([
                'result' => 'deactivated',
        ], 200);
    }

    public function destroy()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:course_modules,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = CourseModule::find(request()->id);
        $data->delete();

        return response()->json([
                'result' => 'deleted',
        ], 200);
    }

    public function restore()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:contact_messages,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = CourseModule::find(request()->id);
        $data->status = 1;
        $data->save();

        return response()->json([
                'result' => 'activated',
        ], 200);
    }

    public function bulk_import()
    {
        $validator = Validator::make(request()->all(), [
            'data' => ['required','array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach (request()->data as $item) {
            $item['created_at'] = $item['created_at'] ? Carbon::parse($item['created_at']): Carbon::now()->toDateTimeString();
            $item['updated_at'] = $item['updated_at'] ? Carbon::parse($item['updated_at']): Carbon::now()->toDateTimeString();
            $item = (object) $item;
            $check = CourseModule::where('id',$item->id)->first();
            if(!$check){
                try {
                    CourseModule::create((array) $item);
                } catch (\Throwable $th) {
                    return response()->json([
                        'err_message' => 'validation error',
                        'errors' => $th->getMessage(),
                    ], 400);
                }
            }
        }

        return response()->json('success', 200);
    }
}
